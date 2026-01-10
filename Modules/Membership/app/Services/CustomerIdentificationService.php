<?php

namespace Modules\Membership\app\Services;

use Modules\Membership\app\Models\LoyaltyCustomer;

class CustomerIdentificationService
{
    /**
     * Identify or create a customer by phone number
     *
     * @param  string  $phone
     * @return LoyaltyCustomer|null
     */
    public function identifyByPhone($phone)
    {
        if (! $phone) {
            return null;
        }

        // Normalize phone number (remove spaces, dashes, etc.)
        $phone = $this->normalizePhone($phone);

        // Try to find existing customer
        $customer = LoyaltyCustomer::byPhone($phone)->first();

        // If not found, create new customer
        if (! $customer) {
            $customer = $this->createCustomer($phone);
        }

        return $customer;
    }

    /**
     * Create a new loyalty customer
     *
     * @param  string  $phone
     * @param  array  $data
     * @return LoyaltyCustomer
     */
    public function createCustomer($phone, $data = [])
    {
        $phone = $this->normalizePhone($phone);

        return LoyaltyCustomer::create([
            'phone' => $phone,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'status' => 'active',
            'joined_at' => now(),
        ]);
    }

    /**
     * Find customer by phone
     *
     * @param  string  $phone
     * @return LoyaltyCustomer|null
     */
    public function findByPhone($phone)
    {
        $phone = $this->normalizePhone($phone);

        return LoyaltyCustomer::byPhone($phone)->first();
    }

    /**
     * Find customer by ID
     *
     * @param  int  $customerId
     * @return LoyaltyCustomer|null
     */
    public function findById($customerId)
    {
        return LoyaltyCustomer::find($customerId);
    }

    /**
     * Normalize phone number
     *
     * @param  string  $phone
     * @return string
     */
    private function normalizePhone($phone)
    {
        // Remove all non-numeric characters except leading +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Ensure it starts with +
        if (! str_starts_with($phone, '+')) {
            // If it doesn't start with +, add country code if needed
            if (strlen($phone) === 10) {
                $phone = '+1'.$phone; // Assuming US for 10-digit numbers
            } elseif (strlen($phone) > 10) {
                $phone = '+'.$phone;
            }
        }

        return $phone;
    }

    /**
     * Check if customer is eligible to earn points
     *
     * @param  LoyaltyCustomer  $customer
     * @return bool
     */
    public function isEligibleToEarn($customer)
    {
        return $customer && $customer->isActive();
    }

    /**
     * Check if customer is eligible to redeem points
     *
     * @param  LoyaltyCustomer  $customer
     * @param  float  $pointsToRedeem
     * @return bool
     */
    public function isEligibleToRedeem($customer, $pointsToRedeem)
    {
        return $customer && $customer->canRedeem($pointsToRedeem);
    }

    /**
     * Update customer info
     *
     * @param  LoyaltyCustomer  $customer
     * @param  array  $data
     * @return LoyaltyCustomer
     */
    public function updateCustomer($customer, $data)
    {
        $customer->update($data);

        return $customer;
    }

    /**
     * Link customer to user account
     *
     * @param  LoyaltyCustomer  $customer
     * @param  int  $userId
     * @return LoyaltyCustomer
     */
    public function linkToUser($customer, $userId)
    {
        $customer->update(['user_id' => $userId]);

        return $customer;
    }

    /**
     * Block customer (no earning/redeeming)
     *
     * @param  LoyaltyCustomer  $customer
     * @return LoyaltyCustomer
     */
    public function blockCustomer($customer)
    {
        $customer->update(['status' => 'blocked']);

        return $customer;
    }

    /**
     * Unblock customer
     *
     * @param  LoyaltyCustomer  $customer
     * @return LoyaltyCustomer
     */
    public function unblockCustomer($customer)
    {
        $customer->update(['status' => 'active']);

        return $customer;
    }

    /**
     * Suspend customer (temporarily)
     *
     * @param  LoyaltyCustomer  $customer
     * @return LoyaltyCustomer
     */
    public function suspendCustomer($customer)
    {
        $customer->update(['status' => 'suspended']);

        return $customer;
    }

    /**
     * Resume customer (after suspension)
     *
     * @param  LoyaltyCustomer  $customer
     * @return LoyaltyCustomer
     */
    public function resumeCustomer($customer)
    {
        $customer->update(['status' => 'active']);

        return $customer;
    }
}
