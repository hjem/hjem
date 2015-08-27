<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Support\ActuatorTypes;
use App\Support\NoMatchException;
use App\Support\ProviderManager;

class ActuatorController extends Controller
{
    
    public function set($type, $value) {
        $type = strtoupper($type);

        try {
            $this->validateType($type);

            $value = ProviderManager::setActuator($type, $value);

            $response = [
                'success' => true
            ];

            if ($value) {
                $response['value'] = $value;
            }
            
            return response()->json($response);

        } catch (NoMatchException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function validateType($type) {
        if (!ActuatorTypes::isValidName($type)) {
            throw new NoMatchException('Unsupported type "' . $type . '"');
        }
    }

}
