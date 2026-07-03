<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Twilio\TwiML\MessagingResponse;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Models\Diplome;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/diplomes', function () {
    return response()->json(Diplome::all());
});


 


Route::post('/twilio/webhook', function (Request $request) {
    $from = $request->input('From'); // numéro WhatsApp du client
    $body = strtolower(trim($request->input('Body')));

    $reply = "Bienvenue 👋 Tape 'commande 123' pour vérifier le statut.";

    if (preg_match('/commande (\d+)/', $body, $matches)) {
        $idCommande = $matches[1];
        $status = \DB::table('commandes')->where('id', $idCommande)->value('status');
        $reply = $status ? "Commande #$idCommande → Statut: $status ✅" : "Commande #$idCommande introuvable ❌";
    }

    $response = new MessagingResponse();
    $response->message($reply);

    return response($response)->header('Content-Type', 'text/xml');
});
