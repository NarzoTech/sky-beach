<?php

namespace Modules\POS\app\Services;

use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Modules\POS\app\Models\PosPrinter;
use Modules\Sales\app\Models\Sale;

class EscPosFormatService
{
    /**
     * Send kitchen ticket to network printer
     */
    public function printKitchenTicket(Sale $sale, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;

        try {
            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("KITCHEN ORDER\n");
            $printer->setTextSize(1, 1);
            $printer->text("ORDER #" . str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT) . "\n");

            if ($sale->table) {
                $printer->setTextSize(2, 2);
                $printer->text("TABLE: " . $sale->table->name . "\n");
                $printer->setTextSize(1, 1);
            }
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            // Info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Waiter:", $sale->waiter->name ?? 'N/A', $width);
            $this->twoColumn($printer, "Guests:", (string)($sale->guest_count ?? 1), $width);
            $printer->setEmphasis(true);
            $printer->text($sale->created_at->format('d-M-Y H:i') . "\n");
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            // Items header
            $printer->setEmphasis(true);
            $this->twoColumn($printer, "ITEM", "QTY", $width);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', $width) . "\n");

            // Items
            foreach ($sale->details as $detail) {
                $itemName = $detail->menuItem->name ?? $detail->service->name ?? 'Item';
                $qty = $detail->quantity . "x";
                $printer->setEmphasis(true);
                $this->twoColumn($printer, $itemName, $qty, $width);
                $printer->setEmphasis(false);

                // Addons
                $addons = $this->parseAddons($detail->addons);
                foreach ($addons as $addon) {
                    $printer->text("  + " . ($addon['qty'] ?? 1) . "x " . $addon['name'] . "\n");
                }

                // Note
                if ($detail->note) {
                    $printer->text("  Note: " . $detail->note . "\n");
                }
            }

            // Special instructions
            if ($sale->special_instructions) {
                $printer->text($this->divider($width));
                $printer->setEmphasis(true);
                $printer->text("SPECIAL INSTRUCTIONS:\n");
                $printer->setEmphasis(false);
                $printer->text($sale->special_instructions . "\n");
            }

            $printer->text($this->divider($width));

            // Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("Total Items: " . $sale->details->sum('quantity') . "\n");
            $printer->setEmphasis(false);

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send kitchen update ticket (new items added)
     */
    public function printKitchenUpdate(Sale $sale, array $newItems, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;

        try {
            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("ADD TO ORDER\n");
            $printer->setTextSize(1, 1);
            $printer->text("ORDER #" . str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT) . "\n");

            if ($sale->table) {
                $printer->setTextSize(2, 2);
                $printer->text("TABLE: " . $sale->table->name . "\n");
                $printer->setTextSize(1, 1);
            }
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            // Info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Waiter:", $sale->waiter->name ?? 'N/A', $width);
            $printer->setEmphasis(true);
            $printer->text(now()->format('d-M-Y H:i') . "\n");
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            $printer->setEmphasis(true);
            $printer->text("NEW ITEMS ADDED:\n");
            $printer->setEmphasis(false);

            foreach ($newItems as $item) {
                $printer->setEmphasis(true);
                $printer->text($item['qty'] . "x " . $item['name'] . "\n");
                $printer->setEmphasis(false);

                if (!empty($item['addons'])) {
                    foreach ($item['addons'] as $addon) {
                        $printer->text("  + " . ($addon['qty'] ?? 1) . "x " . $addon['name'] . "\n");
                    }
                }

                if (!empty($item['note'])) {
                    $printer->text("  Note: " . $item['note'] . "\n");
                }
            }

            $printer->text($this->divider($width));
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("New Items: " . count($newItems) . "\n");
            $printer->setEmphasis(false);

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send cash slip (order slip) to network printer
     */
    public function printCashSlip(Sale $sale, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;
        $setting = cache('setting');

        try {
            // Header - restaurant info
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 1);
            $printer->text(($setting->company_name ?? 'Restaurant') . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            if ($setting->address ?? false) {
                $printer->text($setting->address . "\n");
            }
            $printer->text($this->divider($width));

            // Order info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Order #:", str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT), $width);
            if ($sale->table) {
                $this->twoColumn($printer, "Table:", $sale->table->name, $width);
            }
            $this->twoColumn($printer, "Waiter:", $sale->waiter->name ?? 'N/A', $width);
            $this->twoColumn($printer, "Date:", $sale->created_at->format('d-M-Y H:i'), $width);
            if ($sale->guest_count) {
                $this->twoColumn($printer, "Guests:", (string)$sale->guest_count, $width);
            }
            $printer->text($this->divider($width));

            // Items header
            $printer->setEmphasis(true);
            $printer->text($this->threeColumn("Item", "Qty", "Amount", $width));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', $width) . "\n");

            // Items
            foreach ($sale->details as $detail) {
                $itemName = $detail->menuItem->name ?? $detail->service->name ?? 'Item';
                $qty = (string)$detail->quantity;
                $amount = number_format($detail->price * $detail->quantity, 2);
                $printer->text($this->threeColumn($itemName, $qty, $amount, $width));

                $addons = $this->parseAddons($detail->addons);
                foreach ($addons as $addon) {
                    $addonName = "+ " . $addon['name'];
                    $addonQty = (string)($addon['qty'] ?? 1);
                    $addonAmount = number_format(($addon['price'] ?? 0) * ($addon['qty'] ?? 1), 2);
                    $printer->text($this->threeColumn($addonName, $addonQty, $addonAmount, $width));
                }
            }

            $printer->text($this->divider($width));

            // Totals
            $this->twoColumn($printer, "Subtotal:", number_format($sale->total_price ?? 0, 2), $width);
            if (($sale->total_tax ?? 0) > 0) {
                $this->twoColumn($printer, "Tax:", number_format($sale->total_tax, 2), $width);
            }
            if (($sale->discount_amount ?? 0) > 0) {
                $this->twoColumn($printer, "Discount:", "-" . number_format($sale->discount_amount, 2), $width);
            }

            $printer->text(str_repeat('=', $width) . "\n");
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 1);
            $this->twoColumn($printer, "TOTAL:", number_format($sale->grand_total ?? $sale->total_price ?? 0, 2), (int)floor($width / 2));
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('=', $width) . "\n");

            // Payment status
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("** " . ($sale->payment_status == 1 ? 'PAID' : 'PAYMENT PENDING') . " **\n");
            $printer->setEmphasis(false);

            // Footer
            $printer->text("\nThank you for dining with us!\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send receipt to network printer
     */
    public function printReceipt(Sale $sale, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;
        $setting = cache('setting');

        try {
            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 1);
            $printer->text(($setting->company_name ?? 'Restaurant') . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            if ($setting->address ?? false) {
                $printer->text($setting->address . "\n");
            }
            if ($setting->phone ?? false) {
                $printer->text("Tel: " . $setting->phone . "\n");
            }

            $printer->text(str_repeat('=', $width) . "\n");
            $printer->setEmphasis(true);
            $printer->text("TAX INVOICE / RECEIPT\n");
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            // Receipt info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Receipt #:", str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT), $width);
            $this->twoColumn($printer, "Date:", $sale->created_at->setTimezone('Asia/Dhaka')->format('d-M-Y H:i'), $width);
            if ($sale->table) {
                $this->twoColumn($printer, "Table:", $sale->table->name, $width);
            }
            $this->twoColumn($printer, "Served by:", $sale->waiter->name ?? 'N/A', $width);
            if ($sale->customer) {
                $this->twoColumn($printer, "Customer:", $sale->customer->name, $width);
            }
            $printer->text($this->divider($width));

            // Items header
            $printer->setEmphasis(true);
            $printer->text($this->threeColumn("Qty Description", "", "Amount", $width));
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', $width) . "\n");

            // Items
            foreach ($sale->details as $detail) {
                $itemName = $detail->menuItem->name ?? $detail->service->name ?? 'Item';
                $qty = $detail->quantity;
                $amount = number_format($detail->price * $detail->quantity, 2);
                $printer->text($this->threeColumn($qty . " " . $itemName, "", $amount, $width));

                $addons = $this->parseAddons($detail->addons);
                foreach ($addons as $addon) {
                    $addonQty = $addon['qty'] ?? 1;
                    $addonAmount = number_format(($addon['price'] ?? 0) * $addonQty, 2);
                    $printer->text($this->threeColumn("  " . $addonQty . " + " . $addon['name'], "", $addonAmount, $width));
                }
            }

            $printer->text($this->divider($width));

            // Totals
            $this->twoColumn($printer, "Subtotal:", number_format($sale->subtotal, 2), $width);
            if ($sale->tax_amount > 0) {
                $this->twoColumn($printer, "Tax (" . ($sale->tax_rate ?? 0) . "%):", number_format($sale->tax_amount, 2), $width);
            }
            if ($sale->discount_amount > 0) {
                $this->twoColumn($printer, "Discount:", "-" . number_format($sale->discount_amount, 2), $width);
            }

            $printer->text(str_repeat('=', $width) . "\n");
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 1);
            $this->twoColumn($printer, "GRAND TOTAL:", number_format($sale->total, 2), (int)floor($width / 2));
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('=', $width) . "\n");

            // Payment details
            $payments = $sale->payments ?? collect();
            $paymentCount = $payments->count();

            if ($paymentCount > 0) {
                $printer->setEmphasis(true);
                $printer->text("PAYMENT DETAILS\n");
                $printer->setEmphasis(false);

                $paidAmount = $sale->paid_amount ?? 0;
                $totalAmount = $sale->total ?? $sale->grand_total ?? 0;
                $returnAmount = $paidAmount - $totalAmount;

                if ($paymentCount > 1) {
                    // Split payment
                    foreach ($payments as $p) {
                        $method = ucfirst(str_replace('_', ' ', $p->account->account_type ?? $p->payment_type ?? 'cash'));
                        $this->twoColumn($printer, $method . ":", number_format($p->amount, 2), $width);
                    }
                    if ($returnAmount > 0) {
                        $printer->text($this->divider($width));
                        $this->twoColumn($printer, "Return:", number_format($returnAmount, 2), $width);
                    }
                } else {
                    $firstPayment = $payments->first();
                    $paymentMethod = $firstPayment ? ucfirst(str_replace('_', ' ', $firstPayment->account->account_type ?? $firstPayment->payment_type ?? 'cash')) : 'Cash';
                    $isCashPayment = $firstPayment ? strtolower($firstPayment->account->account_type ?? $firstPayment->payment_type ?? 'cash') === 'cash' : true;

                    if ($isCashPayment) {
                        $this->twoColumn($printer, "Received:", number_format($paidAmount, 2), $width);
                        if ($returnAmount > 0) {
                            $this->twoColumn($printer, "Return:", number_format($returnAmount, 2), $width);
                        }
                    }
                    $printer->text($this->divider($width));
                    $this->twoColumn($printer, "Payment By:", $paymentMethod, $width);
                }
            }

            // Footer
            $printer->text("\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("Thank You For Dining With Us!\n");
            $printer->setEmphasis(false);
            $printer->text("Please Come Again\n");
            if ($setting->website ?? false) {
                $printer->text($setting->website . "\n");
            }
            $printer->text("\nPrinted: " . now()->setTimezone('Asia/Dhaka')->format('d-M-Y H:i:s') . "\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send void ticket to network printer (full order void)
     */
    public function printVoidTicket(Sale $sale, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;

        try {
            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("*** VOID ***\n");
            $printer->setTextSize(1, 1);
            $printer->text("ORDER #" . str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT) . "\n");
            if ($sale->table) {
                $printer->text("TABLE: " . $sale->table->name . "\n");
            }
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            // Info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Waiter:", $sale->waiter->name ?? 'N/A', $width);
            $this->twoColumn($printer, "Voided at:", now()->format('d-M-Y H:i'), $width);
            $printer->text($this->divider($width));

            $printer->setEmphasis(true);
            $printer->text("CANCELLED ITEMS:\n");
            $printer->setEmphasis(false);

            foreach ($sale->details as $detail) {
                $itemName = $detail->menuItem->name ?? $detail->service->name ?? 'Item';
                $printer->text($detail->quantity . "x " . $itemName . "\n");
            }

            $printer->text($this->divider($width));
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("ORDER CANCELLED\n");
            $printer->setEmphasis(false);

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send void item ticket to network printer (single item)
     */
    public function printVoidItemTicket(Sale $sale, $item, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;

        try {
            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("*** VOID ITEM ***\n");
            $printer->setTextSize(1, 1);
            $printer->text("ORDER #" . str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT) . "\n");
            if ($sale->table) {
                $printer->text("TABLE: " . $sale->table->name . "\n");
            }
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Time:", now()->format('d-M-Y H:i'), $width);
            $printer->text($this->divider($width));

            // Voided item
            $itemName = $item->menuItem->name ?? $item->service->name ?? 'Item';
            $printer->setEmphasis(true);
            $printer->text($item->quantity . "x " . $itemName . "\n");
            $printer->setEmphasis(false);

            if ($item->void_reason) {
                $printer->text("\nReason: " . $item->void_reason . "\n");
            }

            $printer->text($this->divider($width));
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("ITEM CANCELLED\n");
            $printer->setEmphasis(false);

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send void items ticket to network printer (multiple items)
     */
    public function printVoidItemsTicket(Sale $sale, $items, PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;

        try {
            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("*** VOID ITEMS ***\n");
            $printer->setTextSize(1, 1);
            $printer->text("ORDER #" . str_pad((string)$sale->id, 6, '0', STR_PAD_LEFT) . "\n");
            if ($sale->table) {
                $printer->text("TABLE: " . $sale->table->name . "\n");
            }
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->twoColumn($printer, "Time:", now()->format('d-M-Y H:i'), $width);
            $printer->text($this->divider($width));

            $printer->setEmphasis(true);
            $printer->text("CANCELLED ITEMS:\n");
            $printer->setEmphasis(false);

            foreach ($items as $item) {
                $itemName = $item->menuItem->name ?? $item->service->name ?? 'Item';
                $printer->text($item->quantity . "x " . $itemName . "\n");
            }

            $printer->text($this->divider($width));
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("ITEMS CANCELLED\n");
            $printer->setEmphasis(false);

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Send a test print to verify network printer connectivity
     */
    public function printTestPage(PosPrinter $posPrinter): void
    {
        $printer = $this->connectPrinter($posPrinter);
        $width = $posPrinter->char_per_line ?? 42;
        $setting = cache('setting');

        try {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 1);
            $printer->text(($setting->company_name ?? 'Restaurant') . "\n");
            $printer->setTextSize(1, 1);
            $printer->text($this->divider($width));
            $printer->setTextSize(2, 2);
            $printer->text("TEST PRINT OK\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text($this->divider($width));
            $printer->text("Printer: " . $posPrinter->name . "\n");
            $printer->text("Type: " . ($posPrinter->type === 'kitchen' ? 'Kitchen' : 'Cash Counter') . "\n");
            $printer->text("IP: " . $posPrinter->ip_address . ":" . $posPrinter->port . "\n");
            $printer->text("Paper: " . $posPrinter->paper_width . "mm\n");
            $printer->text("Profile: " . $posPrinter->capability_profile . "\n");
            $printer->text("Chars/Line: " . $width . "\n");
            $printer->text($this->divider($width));
            $printer->text(now()->format('d-M-Y H:i:s') . "\n");
            $printer->text("Connection successful!\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            try { $printer->close(); } catch (\Exception $ex) {}
            throw $e;
        }
    }

    /**
     * Connect to network printer
     */
    protected function connectPrinter(PosPrinter $posPrinter): Printer
    {
        $connector = new NetworkPrintConnector(
            $posPrinter->ip_address,
            (int)($posPrinter->port ?? 9100)
        );

        $profile = $this->getCapabilityProfile($posPrinter);

        return new Printer($connector, $profile);
    }

    /**
     * Map printer capability profile to library profile
     */
    protected function getCapabilityProfile(PosPrinter $posPrinter): CapabilityProfile
    {
        $profileName = $posPrinter->capability_profile ?? 'default';

        $profileMap = [
            'default' => 'default',
            'simple' => 'simple',
            'SP2000' => 'SP2000',
            'TEP-200M' => 'TEP-200M',
            'P822D' => 'P822D',
        ];

        $profile = $profileMap[$profileName] ?? 'default';

        return CapabilityProfile::load($profile);
    }

    /**
     * Create a dashed divider line
     */
    protected function divider(int $width): string
    {
        return str_repeat('-', $width) . "\n";
    }

    /**
     * Print two columns (left-aligned label, right-aligned value)
     */
    protected function twoColumn(Printer $printer, string $left, string $right, int $width): void
    {
        $gap = $width - mb_strlen($left) - mb_strlen($right);
        if ($gap < 1) {
            $printer->text($left . "\n");
            $printer->text(str_pad('', $width - mb_strlen($right)) . $right . "\n");
        } else {
            $printer->text($left . str_repeat(' ', $gap) . $right . "\n");
        }
    }

    /**
     * Format three columns (item name, qty, amount)
     */
    protected function threeColumn(string $left, string $center, string $right, int $width): string
    {
        $rightWidth = max(mb_strlen($right), 8);
        $centerWidth = max(mb_strlen($center), 4);
        $leftWidth = $width - $rightWidth - $centerWidth;

        if (mb_strlen($left) > $leftWidth) {
            $left = mb_substr($left, 0, $leftWidth);
        }

        return str_pad($left, $leftWidth) . str_pad($center, $centerWidth, ' ', STR_PAD_LEFT) . str_pad($right, $rightWidth, ' ', STR_PAD_LEFT) . "\n";
    }

    /**
     * Parse addons from detail
     */
    protected function parseAddons($addons): array
    {
        if (empty($addons)) {
            return [];
        }

        if (is_string($addons)) {
            $addons = json_decode($addons, true);
        }

        return is_array($addons) ? $addons : [];
    }
}
