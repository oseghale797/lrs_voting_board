<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use App\Models\PublishedKey;
use App\Models\Signature;
use App\Models\Vote;
use App\Models\VoterKey;
use App\Models\VotingLink;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller
{
    private function validator($request)
    {
        return Validator::make(
            $request->all(),
            [
                'id' => ['required', 'string'],
                'token' => ['required', 'string'],
                // 'public_key' => ['required', 'string'],
            ]
        );
    }
    public function index(Request $request)
    {
        $validator = $this->validator($request);
       
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $id = $request->id;
        $token = $request->token;
        $agendas = Agenda::where('id', $id)
        ->whereHas('votingLink', function ($query) use ($token) {
            $query->where('token', $token);
        })
        ->get();
        return view('welcome');
    }

    public function votingPage(Request $request)
    {
        $validator = $this->validator($request);
       
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $id = $request->id;
        $token = $request->token;
        $agenda = Agenda::where('id', $id)
        ->with(['agendaAnswers', 'signature'])
        ->first();
        return view('vote', compact('agenda'));
    }

    public function generateKey(Request $request)
    {
       try {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => ['required', 'string'],
                'token' => ['required', 'string'],
                'public_key' => ['required', 'string'],
            ]
        );
       
        if ($validator->fails()) {
            $error = implode(',', $validator->errors()->all());
            return response()->json(['status' => false,'message' => $error], 200);
        }
       
        VoterKey::create([
            'pk' => $request->public_key
        ]);
        
        return response()->json(['status' => true, 'message' => 'Key generated successfully. Publish your public key to the bulletin board'], 200 );

       } catch (\Throwable $th) {
        Log::error('Generate key error: '. $th->getMessage());
        return response()->json(['status' => false,'message' => 'A server error occurred']);
       }
        
    }

    public function publishKey(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['required', 'string'],
                    'token' => ['required', 'string'],
                    'public_key' => ['required', 'string'],
                ]
            );
           
            if ($validator->fails()) {
                $error = implode(',', $validator->errors()->all());
                return response()->json(['status' => false,'message' => $error], 200);
            }

            $id = $request->id;
            $token = $request->token;
            $public_key = $request->public_key;

            $verifyVotingLink = $this->verifyVotingLink($id, $token);
            if ($verifyVotingLink == null) {
               return response()->json(['status' => false, 'message' => 'Unknown voting link'], 403 );
            }
            $voterKey = VoterKey::where('pk', $public_key)->first();

            if (! $voterKey) {

                return response()->json(['status' => false, 'message' => 'Key not found or invalid.'], 404);
            }
            //make sure more than 3 public keys are not published for the same agenda
            $counter = PublishedKey::where('agenda_id', $id)->count();
            // $signature = Signature::whereIn('pk_signer', $publishedKey->pluck('pk_signer'))->first();

            if ($counter > 3 ) {
                return response()->json(['status' => true, 'message' => 'Maximum public keys published', 'counter' => $counter], 200 );
            }

            PublishedKey::create([
                'agenda_id' => $request->id,
                'pk' => $voterKey->pk,
            ]);

            return response()->json(['status' => true, 'message' => 'Public key published. Wait while we form a link'], 200 );
    
           } catch (\Throwable $th) {
            Log::error('Publish key error: '. $th->getMessage());
            return response()->json(['status' => false,'message' => 'A server error occurred']);
           }
    }

    public function countPublishedKeys(Request $request)
    {
        $publishedKeys = PublishedKey::where('agenda_id', $request->id)->limit(3)->get();
        $publicKeys = $publishedKeys->pluck('pk');
        return response()->json(['status' => true, 'count' => $publishedKeys->count(),  'publishedKeys' => $publicKeys, 'id'=>$request->id ]);
    }

    public function signVote(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['required', 'string'],
                    // 'token' => ['required', 'string'],
                    'public_key' => ['required', 'string'],
                    'linkability_tag' => ['required', 'string'],
                    'signature' => ['required', 'string'],
                    'message' => ['required', 'string']
                ]
            );
           
            if ($validator->fails()) {
                $error = implode(',', $validator->errors()->all());
                return response()->json(['status' => false,'message' => $error], 200);
            }
            $counter = PublishedKey::where('agenda_id', $request->id)->count();
            
            if ($counter > 3 ) {
                return response()->json(['status' => true, 'count' => $counter, 'message' => 'Maximum public keys published'], 200 );
            }
            $linkabilityTag = $request->input('linkability_tag');
            $message = $request->input('message');
            $signature = $request->input('signature');
            $pkSigner = $request->input('public_key');
            $existingVote = Signature::where('linkability_tag', $linkabilityTag)->first();

            if ($existingVote) {
                // Prevent double-voting
                return response()->json(['status' => false, 'message' => 'Double-voting detected!']);
            }
            $agenda = $this->getPublishedKeys($request->id)->first(); 
            
            if ($agenda && $agenda->votingLink) {
                
                 Signature::create([
                    'agenda_id' => $request->id,
                    'pk_signer' => $pkSigner,
                    'signature' => $signature,
                    'message' => $message,
                    'linkability_tag' => $linkabilityTag,
                ]);
    
                return response()->json(['status' => true, 'message' => "Everything looks good, you'll be redirected shortly."], 200);
    
            } else {
                return response()->json(['status' => false, 'message' => 'Agenda or Voting Link not found.'], 200);
            }
        } catch (\Throwable $th) {
            Log::error('Sign message error: '. $th->getMessage());
            return response()->json(['status' => false, 'message' => 'A server error occurred']);
        }
    }
    
    private function verifyVotingLink($id, $token)
    {
        $agendas = Agenda::where('id', $id)
        ->whereHas('votingLink', function ($query) use ($token) {
            $query->where('token', $token);
        })
        ->get();
        return $agendas != null ? $agendas : null;
    }

    public function vote(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['required', 'string'],
                    'signer_public_key' => ['required', 'string']
                ]
                );
           
            if ($validator->fails()) {
                $errors = implode('', $validator->errors()->all());
                return response()->json(['status' => false, 'message' => $errors], 200);  
            }
            $id = $request->id;
            $agenda_answer_id = $request->answer_id;
            $signerPk = $request->signer_public_key;
            $agenda = $this->getPublishedKeys($id)->first();
            $counter = PublishedKey::where('agenda_id', $request->id)->count();
            // $signature = Signature::whereIn('pk_signer', $publishedKey->pluck('pk_signer'))->first();

            if ($counter > 3 ) {
                return response()->json(['status' => true, 'message' => 'Maximum public keys published', 'count' => $counter], 200 );
            }
            $signature = Signature::where('pk_signer', $signerPk)->where('agenda_id', $id)->first();
            if (!$signature) {
                return response()->json(['status' => false, 'message' => 'This voter is not part of the ring'], 200);  
            }
            $isVoted =  Vote::where('linkability_tag', $signature->linkability_tag)->first();
            if (! $isVoted) {
                Vote::create([
                    'voting_link_id'=> $agenda->votingLink->id,
                    'public_key' => $signerPk,
                    'agenda_answer_id' => $agenda_answer_id,
                    'linkability_tag' => $signature->linkability_tag
                ]);
                return response()->json(['status' => true, 'message' => 'Voted successfully']);
            }
            return response()->json(['status' => true, 'message' => 'Already voted']);
            
        } catch (\Throwable $th) {
            Log::error('Voting error: '. $th->getMessage());
            return response()->json(['status' => false, 'message' => 'A server error occurred']);
        }
    }

    public function showPublishKey(Request $request)
    {
        $validator = $this->validator($request);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
       
        return view('publish-key');
    }

    private function getPublishedKeys($id)
    {
        return Agenda::where('id', $id)->with(['publishedKeys', 'votingLink']);
    }

    public function success(Request $request)
    {
        $validator = $this->validator($request);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        return view('success');
    }

    public function showBulletin(Request $request)
    {
        $validator = $this->validator($request);
       
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $id = $request->id;
        $token = $request->token;
        $agendas = Agenda::whereId($id)->with([
            'agendaAnswers' => function ($query) {
                // Load only agenda answers that have votes
                $query->whereHas('votes'); // Use the new relationship name
            },
            'agendaAnswers.votes' => function ($query) {
                // Load all votes with the corresponding public key
                $query->select('public_key', 'agenda_answer_id');
            }
        ])
        // ->withCount('agendaAnswers.votes as votes_count') // Uncomment if needed
        ->get();
        return view('bulletin', compact('agendas'));
    }

}


