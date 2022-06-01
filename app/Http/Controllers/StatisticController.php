<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Statistic;
use DB;

class StatisticController extends Controller
{
    public function create(Request $request){

        // Se validan la fecha y monto
        $validated = $request->validate([
            'date' => 'required|max:255',
            'amount' => 'required|max:255',
        ]);
        try {
            // Se crea el objeto, se pasan los datos y se ingresa en la base de datos

            $statistic = new Statistic();
            $statistic->date = $request->date;
            $statistic->amount = $request->amount;
            $statistic->user_id = Auth::user()->id;

            $statistic->save();

            $statistics = $this->read();

            return response()->json(['status' => 1, 'message' => 'Se agregaron los datos correctamente', 'data' => $statistics]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 2, 'message' => 'Ocurrió un problema al agregar los datos. '.$th->getMessage()]);
        }
    }

    public function read(){
        try {
            // Obtiene todas los datos asociados al usuario logeado, los agrupa y ordena por fecha
            // sumando los montos que son de la misma fecha
            $statistics = Statistic::where('user_id', Auth::user()->id)
                                    ->select(
                                        'date',
                                        DB::raw('SUM(amount) as value')
                                    )
                                    ->groupBy('date')
                                    ->orderBy('date', 'ASC')
                                    ->get();
            return $statistics;
        } catch (\Throwable $th) {
            return [];
        }
    }
}
