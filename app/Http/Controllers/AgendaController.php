<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgendaRequest;
use App\Models\Agenda;
use App\Models\AgendaAnswer;
use App\Models\VotingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('agenda');

    }

    private function validator($request)
    {
        return Validator::make(
            $request->all(),
            [
                'title' => ['required', 'string'],
                'description' => ['nullable', 'string'],
                'answer' => ['required', 'array'],
                'answer.*' => ['required', 'string', 'max:255'],
            ]
        );
    }
    

    public function store(Request $request)
    {
        try {
            $validator = $this->validator($request);
       
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        
        
            $agenda = Agenda::create([
                'title' => $request->input('title'),
                'description' => $request->input('description') ?? null,
            ]);
            
            $answers = $request->input('answer');
            
            foreach ($answers as $answer) {
                $agenda->agendaAnswers()->create([
                    'answer' => $answer
                ]);
            }
        
            return redirect()->back()->withSuccess('Agendas created successfully.');
        } catch (\Throwable $th) {
            Log::error('Create Agenda error: '. $th->getMessage());
            return redirect()->back()->withErrors('A server error occurred')->withInput();
        }
    }

    public function generateVotingLink($id)
    {
        try {
            $agenda = Agenda::find($id);

            if ($agenda->votingLink) {
                $token = $agenda->votingLink->token;
            } else {
                $token = $this->generateVotingToken();
                $agenda->votingLink()->create([
                    'token' => $token,
                ]);
            }

            $url = route('key.generate', ['id' => $id, 'token' => $token]);

            return response()->json(['status' => true, 'url' => $url]);
        } catch (\Throwable $th) {
            Log::error('Create Agenda error: '. $th->getMessage());
            return response()->json(['status' => false,'message' => 'A server error occurred']);
        }
    }

    private function generateVotingToken()
    {
        $token = Str::random(32) . time();
        while (VotingLink::where('token', $token)->exists()) {
            $token = Str::random(32) . time();
        }
        return $token;
    }

    public function delete($id)
    {
        $agenda = Agenda::find($id);
        if ($agenda) {
            $agenda->delete();
            return redirect()->back()->withSuccess('Successfully deleted');
        }
        return redirect()->back()->withErrors('Agenda not found');
    }


}
