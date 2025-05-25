<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where("user_id", Auth::id())->get();
        if ($clients->isEmpty()) {
            return response()->json(["message" => "No clients found"], 404);
        }

        return response()->json($clients, 200);
    }

    public function show($id)
    {
        $client = Client::where('id', $id)->where("user_id", Auth::id())->first();
        if (!$client) {
            return response()->json(["message" => "Client not found"], 404);
        }

        return response()->json($client, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255",
            "contact_person" => "required|string|max:255",
        ]);

        $client = Client::create([
            "user_id" => Auth::id(),
            "name" => $request->name,
            "email" => $request->email,
            "contact_person" => $request->contact_person
        ]);

        return response()->json($client, 201);

    }


    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        if (Auth::id() !== $client->user_id) {
            return response()->json(["message" => "Invalid client"], 403);
        }
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255",
            "contact_person" => "required|string|max:255",
        ]);

        $client->update($request->only(["name", "email", "contact_person"]));

        return response()->json(["message" => "Client Update", "client" => $client], 200);
    }


    public function delete($id)
    {
        $client = Client::findOrFail($id);

        if (Auth::id() !== $client->user_id) {
            return response()->json(["message" => "Invalid Client"], 403);
        }

        $client->delete();
        return response()->json(["message" => "Client deleted"], 200);

    }
}
