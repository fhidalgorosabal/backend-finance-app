<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;

class SettingController extends Controller
{
    use ResponseApi;

    /**
     * Display setting.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSetting()
    {
        $setting = Setting::find('1');   
        return $this->responseData($setting, 'Configuración del sistema');  
    }

    /**
     * Change of month
     * 
     * @param  int  $month
     * @return \Illuminate\Http\Response
     */
    public function changeMonth(Request $request) {
        try {    
            $validatedData = $request->validate([
                'month' => 'required|numeric'
            ]);

            $setting = Setting::find('1');   
            $setting->current_month = $validatedData['month'];
            $setting->save();
            return $this->responseData($setting, 'Se ha realizado el cambio de mes correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo realizar el cambio de mes.');
        }
    }
}
