<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\User;

class TicketController extends Controller
{
    public function getAllTickets() {
      $tickets = Ticket::get()->toJson(JSON_PRETTY_PRINT);
      return response($tickets, 200);
    }

    public function createTicket(Request $request) {

      if(Ticket::where('timing', '=', $request->timing)->count() >= 20){
          return response()->json([
            "message" => "Already 20 tickets for this timing"
        ], 200);
      }

      $user = User::where('phone', '=', $request->phone)->first();
      if($user === null) {
          $user = new User;
      }
      $user->phone = $request->phone;
      $user->name = $request->name;
      $user->save();

      $ticket = new Ticket;
      $ticket->title = $request->title;
      $ticket->timing = $request->timing;
      $ticket->expiry = date('Y-m-d H:i:s',strtotime('+8 hours',strtotime($request->timing)));
      $ticket->user_id = $user->id;
      $ticket->save();

      return response()->json([
        "message" => "ticket record created",
        "ticket" => $ticket,
        "user" => $user
      ], 201);
    }

    public function getTicket(Request $request) {
      if (Ticket::where('timing', $request->timing)->exists()) {
        $ticket = Ticket::where('timing', $request->timing)->get()->toJson(JSON_PRETTY_PRINT);
        return response($ticket, 200);
      } else {
        return response()->json([
          "message" => "Ticket not found"
        ], 404);
      }
    }

    public function updateTicket(Request $request) {
      if (Ticket::where('id', $request->ticket_id)->exists()) {
        $ticket = Ticket::find($request->ticket_id);

        $ticket->timing = is_null($request->timing) ? $ticket->timing : $request->timing;
        $ticket->save();

        return response()->json([
          "message" => "records updated successfully",
          "ticket" => $ticket
        ], 200);
      } else {
        return response()->json([
          "message" => "Ticket not found"
        ], 404);
      }
    }

    public function deleteTicket (Request $request) {
      if(Ticket::where('id', $request->ticket_id)->exists()) {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->delete();

        return response()->json([
          "message" => "record deleted with id: ".$request->ticket_id
        ], 202);
      } else {
        return response()->json([
          "message" => "Ticket not found"
        ], 404);
      }
    }

    public function getUser(Request $request) {
        if (Ticket::where('id', $request->ticket_id)->exists()) {
          $user = Ticket::where('id',$request->ticket_id)->first()->user;
          return response($user, 200);
        } else {
          return response()->json([
            "message" => "Ticket not found"
          ], 404);
        }
    }

}
