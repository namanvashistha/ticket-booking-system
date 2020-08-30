<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;

class TicketController extends Controller
{
    public function getAllTickets() {
      $tickets = Ticket::get()->toJson(JSON_PRETTY_PRINT);
      return response($tickets, 200);
    }

    public function createTicket(Request $request) {

      $user = User::where('phone', '=', $request->phone)->first();
      if($user === null) {
          $user = new User;
      }
      $user->phone = $request->phone;

      $ticket = new Ticket;
      $ticket->title = $request->name;
      $ticket->timing = $request->time;
      $ticket->user_id = $user->id;
      $ticket->save();

      return response()->json([
        "message" => "ticket record created"
      ], 201);
    }

    public function getTicket($id) {
      if (Ticket::where('id', $id)->exists()) {
        $ticket = Ticket::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
        return response($ticket, 200);
      } else {
        return response()->json([
          "message" => "Ticket not found"
        ], 404);
      }
    }

    public function updateTicket(Request $request, $id) {
      if (Ticket::where('id', $id)->exists()) {
        $ticket = Ticket::find($id);

        $ticket->title = is_null($request->name) ? $ticket->title : $request->name;
        $ticket->user_id = is_null($request->user) ? $ticket->user_id : $request->user;
        $ticket->save();

        return response()->json([
          "message" => "records updated successfully"
        ], 200);
      } else {
        return response()->json([
          "message" => "Ticket not found"
        ], 404);
      }
    }

    public function deleteTicket ($id) {
      if(Ticket::where('id', $id)->exists()) {
        $ticket = Ticket::find($id);
        $ticket->delete();

        return response()->json([
          "message" => "records deleted"
        ], 202);
      } else {
        return response()->json([
          "message" => "Ticket not found"
        ], 404);
      }
    }
}
