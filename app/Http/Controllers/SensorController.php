<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Support\SensorTypes;
use App\Support\NoMatchException;
use App\Support\ProviderManager;

class SensorController extends Controller
{

    public function query($type, $name = null) {
        $type = strtoupper($type);

        try {
            $this->validateType($type);
            
            return response()->json(ProviderManager::getDataForQuery($type, $name));

        } catch (NoMatchException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function validateType($type) {
        if (!SensorTypes::isValidName($type)) {
            throw new NoMatchException('Unsupported type "' . $type . '"');
        }
    }

}
