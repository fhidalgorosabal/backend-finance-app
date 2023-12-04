<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ResponseApi;

class SettingController extends Controller
{
    use ResponseApi;

    /**
     * Display system configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSetting(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'company_id' => 'required'
            ]);

            $setting = Setting::join('companies', 'settings.company_id', '=', 'companies.id')
                ->where('settings.company_id', $validatedData['company_id'])
                ->select([
                    'settings.current_year',
                    'settings.current_month',
                    'companies.company_code',
                    'companies.company_name'
                ])->first();

            if ($setting) {
                return $this->responseData($setting, 'Configuración del sistema');
            } else {
                throw new \Exception('Los datos de esta compañía no están registrados.');
            }

        } catch (\Exception $e) {
            return $this->responseError($e, 'No es posible obtener la configuración.');
        }
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
                'month' => 'required|numeric',                
                'company_id' => 'required'
            ]);

            $setting = Setting::where('company_id', $validatedData['company_id'])->first();  
            $setting->update(['current_month' => $validatedData['month']]); 
            return $this->responseData($setting, 'Se ha realizado el cambio de mes correctamente.');
        } catch (\Exception $e) {
            return $this->responseError($e, 'No se pudo realizar el cambio de mes.');
        }
    }
}
