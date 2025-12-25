<?php

namespace App\Http\Controllers;

use App\Exports\StockExport;
use App\Models\Stock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;

class StockController extends Controller
{
    public function __construct(private ProductService $product, private BrandService $brandService, private ProductCategoryService $categoryService,) {}

    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('stock.view');
        $query = $this->product->allActiveProducts($request);

        if (request('keyword')) {
            $query = $query->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('sku', 'like', '%' . request()->keyword . '%')
                    ->orWhere('barcode', 'like', '%' . request()->keyword . '%');
            });
        }
        if (request('order_by')) {
            $query = $query->orderBy('id', request('order_by'));
        }
        if (request('brand_id')) {
            $query = $query->where('brand_id', request('brand_id'));
        }
        if (request('category_id')) {
            $query = $query->where('category_id', request('category_id'));
        }
        if (request('stock_status')) {
            if (request('stock_status') == 'in_stock') {
                $query = $query->where('stock', '>', 0);
            }
            if (request('stock_status') == 'out_of_stock') {
                $query = $query->where('stock', '=<', 0);
            }
        }
        // Calculate totals from all filtered data before pagination
        $allProducts = (clone $query)->with('stockDetails')->get();
        $totals = [
            'totalInQty' => 0,
            'totalOutQty' => 0,
            'totalStock' => 0,
            'totalStockPP' => 0,
            'totalStockSP' => 0,
        ];
        foreach ($allProducts as $product) {
            $stock = $product->stock < 0 ? 0 : $product->stock;
            $selling_price = $product->selling_price ?? 0;
            $totals['totalInQty'] += $product->stockDetails->sum('in_quantity');
            $totals['totalOutQty'] += $product->stockDetails->sum('out_quantity');
            $totals['totalStock'] += $product->stock;
            $totals['totalStockPP'] += remove_comma($stock) * remove_comma($product->avg_purchase_price);
            $totals['totalStockSP'] += remove_comma($stock) * remove_comma($selling_price);
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $query->get();  // No pagination, return all results
        } else {
            $products = $query->paginate($parpage);  // Paginate results
            $products->appends(request()->query());
        }

        if (checkAdminHasPermission('stock.excel.download')) {
            if (request('export')) {
                $fileName = 'stock-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new StockExport($allProducts, $totals), $fileName);
            }
        }

        if (checkAdminHasPermission('stock.pdf.download')) {
            if (request('export_pdf')) {
                return view('admin.pages.stock.pdf.stock', [
                    'products' => $allProducts,
                    'totals' => $totals,
                ]);
            }
        }

        $brands = $this->brandService->getActiveBrands();
        $categories = $this->categoryService->getAllProductCategoriesForSelect();
        return view('admin.pages.stock.stock', compact('products', 'brands', 'categories', 'totals'));
    }

    public function ledger($id)
    {
        checkAdminHasPermissionAndThrowException('stock.ledger');
        $product = $this->product->getProduct($id);
        $stocks = Stock::where('product_id', $id)->orderBy('date', 'asc')->paginate(20);

        $stocks->appends(request()->query());
        return view('admin.pages.stock.ledger', compact('product', 'stocks'));
    }

    public function reset($id)
    {
        checkAdminHasPermissionAndThrowException('stock.reset');
        $this->resetStock($id);

        return redirect()->back()->with(['messege' => 'Stock Reset Successfully', 'alert-type' => 'success']);
    }

    public function resetAll()
    {
        checkAdminHasPermissionAndThrowException('stock.reset');
        Stock::truncate();

        $products = $this->product->getProducts()->get();

        foreach ($products as $product) {
            $this->resetStock($product->id);
        }

        return redirect()->back()->with(['messege' => 'All Stock Reset Successfully', 'alert-type' => 'success']);
    }

    private function resetStock($id)
    {
        $product = $this->product->getProduct($id);
        Stock::where('product_id', $id)->delete();
        $product->update(['stock' => 0, 'stock_status' => 'out_of_stock']);

        Stock::create([
            'product_id' => $product->id,
            'date' => now(),
            'type' => '	Opening Stock',
            'in_quantity' => 0,
            'available_qty' => 0,
            'sku' => $product->sku,
            'purchase_price' => 0,
            'rate' => 0,
            'sale_price' => $product->price,
            'tax' => 0,
            'created_by' => auth('admin')->user()->id,
        ]);
    }
}
