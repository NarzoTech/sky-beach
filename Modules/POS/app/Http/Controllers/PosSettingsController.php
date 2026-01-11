<?php

namespace Modules\POS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\POS\app\Models\PosSettings;

class PosSettingsController extends Controller
{
    use RedirectHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pos_settings = PosSettings::first();

        // Create default settings if none exist
        if (!$pos_settings) {
            $pos_settings = new PosSettings([
                'show_phone' => true,
                'show_address' => true,
                'show_email' => true,
                'show_customer' => true,
                'show_warehouse' => false,
                'show_discount' => true,
                'show_barcode' => false,
                'show_note' => true,
                'is_printable' => true,
                'merge_cart_items' => true,
            ]);
        }

        return view('pos::settings', compact('pos_settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pos::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
      try
        {
            $data = $request->all();
            $pos_settings = PosSettings::first();
            if($pos_settings)
            {
                $pos_settings->update($data);
            }
            else
            {
                PosSettings::create($data);
            }
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.pos.settings',[], ['messege'=> 'Settings Updated Successfully','alert-type'=>'success']);
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.pos.settings',[], ['messege'=> 'Something went wrong','alert-type'=>'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('pos::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('pos::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
