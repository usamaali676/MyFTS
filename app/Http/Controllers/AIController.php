<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\KnowledgeRetrievalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class AIController extends Controller
{


     private const MODEL = 'gpt-4o-mini'; // or whatever you use

private const PROMPT_TEMPLATE = <<<'PROMPT'
    Today's date is {date}.

    A visitor on the FirmTech Services website has asked the following question.
    Use the KNOWLEDGE provided in the system message to answer clearly and
    helpfully, focusing on our services and pricing.

    Customer message: {message}
    PROMPT;

private const SYSTEM_PROMPT = <<<'PROMPT'
    You are the AI assistant for FirmTech Services, a digital marketing and
    web development agency. Your job is to help website visitors understand
    what FirmTech offers and what it costs, using only the KNOWLEDGE section
    provided below.

    Guidelines:
    - Answer using only the information in the KNOWLEDGE section. Do not
      invent services, features, prices, discounts, or timelines that
      aren't listed there.
    - When asked about pricing, give the specific numbers or ranges from
      the KNOWLEDGE if available. If exact pricing isn't listed, say what
      is known and suggest they contact FirmTech for a custom quote.
    - Be warm, concise, and helpful — like a knowledgeable sales rep, not
      pushy or overly salesy.
    - Where it fits naturally, guide the customer toward a next step
      (requesting a quote, booking a call, contacting the team).
    - If the question isn't covered by the KNOWLEDGE at all, say you
      don't have that information and suggest contacting FirmTech
      directly rather than guessing.
    - Never make up contact details, guarantees, or promises not present
      in the KNOWLEDGE.
    PROMPT;

    public function index()
    {
        return view('pages.ai-assistant.index');
    }


    // NEW: inject the retrieval service. Laravel resolves this
    // automatically — no service provider bindings needed since it has
    // no constructor dependencies of its own.
    public function __construct(
        private readonly KnowledgeRetrievalService $knowledge,
    ) {}

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:10000',
            'session_id' => 'required|uuid',
            'date'       => 'nullable|date',
        ]);

        $userId      = Auth::id();
        $sessionId   = $request->session_id;
        $userMessage = trim($request->message);
        $reportDate  = $request->date;
        $dateLabel   = $reportDate ?? 'Not specified';

        $finalPrompt = str_replace(
            ['{date}', '{message}'],
            [$dateLabel, $userMessage],
            self::PROMPT_TEMPLATE
        );

        // NEW: retrieve the top matching Markdown knowledge files for this
        // question (local file scoring only — no AI, no embeddings).
        $relevantDocs = $this->knowledge->retrieve($userMessage);
        $knowledgeContext = $this->knowledge->buildKnowledgeContext($relevantDocs);

        // ---------------------------------------------------------------
        // TESTING MODE: DB logging disabled below. See "RE-ENABLE" notes.
        // ---------------------------------------------------------------

        // RE-ENABLE: restore this block to log the pending conversation
        // before calling OpenAI, exactly as your original code did.
        /*
        $conversation = AiConversation::create([
            'user_id'         => $userId,
            'session_id'      => $sessionId,
            'report_date'     => $reportDate,
            'prompt_template' => self::PROMPT_TEMPLATE,
            'final_prompt'    => $finalPrompt,
            'user_message'    => $userMessage,
            'ai_response'     => null,
            'model'           => self::MODEL,
            'tokens_used'     => null,
            'status'          => 'pending',
            // Optional: if you add a `knowledge_context` text column, you
            // can log exactly what was retrieved/injected for this turn:
            // 'knowledge_context' => $knowledgeContext,
        ]);
        */
        $conversation = null; // stub while DB logging is disabled

        try {
            $response = OpenAI::chat()->create([
                'model'    => self::MODEL,
                // CHANGED: was a single ['role' => 'user', 'content' => $finalPrompt]
                // message. Now a system message (instructions + retrieved
                // knowledge) precedes the user's actual prompt.
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => self::SYSTEM_PROMPT . "\n\nKNOWLEDGE:\n" . $knowledgeContext,
                    ],
                    [
                        'role'    => 'user',
                        'content' => $finalPrompt,
                    ],
                ],
            ]);

            $aiResponse = $response->choices[0]->message->content;
            $tokensUsed = $response->usage->totalTokens;
            $modelUsed  = $response->model;

            // RE-ENABLE: restore this block to persist the completed response.
            /*
            $conversation->update([
                'ai_response' => $aiResponse,
                'tokens_used' => $tokensUsed,
                'model'       => $modelUsed,
                'status'      => 'completed',
            ]);
            */

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'               => $conversation->id ?? null,
                    'session_id'       => $sessionId,
                    'ai_response'      => $aiResponse,
                    // Handy while testing retrieval — remove once satisfied:
                    'matched_sources'  => $relevantDocs->pluck('path')->values(),
            //         'created_at' => $conversation->created_at?->format('M d, Y H:i')
            //    ?? now()->format('M d, Y H:i'),
                ],
            ]);

        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            // RE-ENABLE:
            // $conversation->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please wait a moment and try again.',
            ], 429);

        } catch (\OpenAI\Exceptions\ErrorException $e) {
            // RE-ENABLE:
            // $conversation->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'The assistant encountered an error. Please try again.',
            ], 422);

        } catch (\OpenAI\Exceptions\ServerException | \OpenAI\Exceptions\TransporterException $e) {
            // RE-ENABLE:
            // $conversation->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'The service is temporarily unavailable. Please try again shortly.',
            ], 503);

        } catch (\Exception $e) {
            // RE-ENABLE:
            // $conversation->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('AI chat unexpected error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    // public function sendMessage(Request $request)
    // {
    //     $request->validate([
    //         'message'    => 'required|string|max:10000',
    //         'session_id' => 'required|uuid',
    //         'date'       => 'nullable|date',
    //     ]);

    //     $userId     = Auth::id();
    //     $sessionId  = $request->session_id;
    //     $userMessage = trim($request->message);
    //     $reportDate  = $request->date;
    //     $dateLabel   = $reportDate ?? 'Not specified';

    //     $finalPrompt = str_replace(
    //         ['{date}', '{message}'],
    //         [$dateLabel, $userMessage],
    //         self::PROMPT_TEMPLATE
    //     );

    //     // Create the record in pending state before calling the API
    //     $conversation = AiConversation::create([
    //         'user_id'         => $userId,
    //         'session_id'      => $sessionId,
    //         'report_date'     => $reportDate,
    //         'prompt_template' => self::PROMPT_TEMPLATE,
    //         'final_prompt'    => $finalPrompt,
    //         'user_message'    => $userMessage,
    //         'ai_response'     => null,
    //         'model'           => self::MODEL,
    //         'tokens_used'     => null,
    //         'status'          => 'pending',
    //     ]);

    //     try {
    //         $response = OpenAI::chat()->create([
    //             'model'    => self::MODEL,
    //             'messages' => [
    //                 ['role' => 'user', 'content' => $finalPrompt],
    //             ],
    //         ]);

    //         $aiResponse  = $response->choices[0]->message->content;
    //         $tokensUsed  = $response->usage->totalTokens;
    //         $modelUsed   = $response->model;

    //         $conversation->update([
    //             'ai_response' => $aiResponse,
    //             'tokens_used' => $tokensUsed,
    //             'model'       => $modelUsed,
    //             'status'      => 'completed',
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'data'    => [
    //                 'id'          => $conversation->id,
    //                 'session_id'  => $sessionId,
    //                 'ai_response' => $aiResponse,
    //                 'created_at'  => $conversation->created_at->format('M d, Y H:i'),
    //             ],
    //         ]);

    //     } catch (\OpenAI\Exceptions\RateLimitException $e) {
    //         $conversation->update([
    //             'status'        => 'failed',
    //             'error_message' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Too many requests. Please wait a moment and try again.',
    //         ], 429);

    //     } catch (\OpenAI\Exceptions\ErrorException $e) {
    //         $conversation->update([
    //             'status'        => 'failed',
    //             'error_message' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'The assistant encountered an error. Please try again.',
    //         ], 422);

    //     } catch (\OpenAI\Exceptions\ServerException | \OpenAI\Exceptions\TransporterException $e) {
    //         $conversation->update([
    //             'status'        => 'failed',
    //             'error_message' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'The service is temporarily unavailable. Please try again shortly.',
    //         ], 503);

    //     } catch (\Exception $e) {
    //         $conversation->update([
    //             'status'        => 'failed',
    //             'error_message' => $e->getMessage(),
    //         ]);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An unexpected error occurred. Please try again.',
    //         ], 500);
    //     }
    // }

    public function history(Request $request)
    {
        $query = $request->input('q', '');

        $conversations = AiConversation::where('user_id', Auth::id())
            ->when($query, fn ($q) => $q->where('user_message', 'like', "%{$query}%"))
            ->orderByDesc('created_at')
            ->get(['id', 'session_id', 'user_message', 'status', 'created_at'])
            ->unique('session_id')
            ->values()
            ->map(fn ($c) => [
                'id'          => $c->id,
                'session_id'  => $c->session_id,
                'preview'     => Str::limit($c->user_message, 60),
                'status'      => $c->status,
                'created_at'  => $c->created_at->format('M d, Y'),
            ]);

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    public function show($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);

        $messages = AiConversation::where('user_id', Auth::id())
            ->where('session_id', $conversation->session_id)
            ->orderBy('created_at')
            ->get(['id', 'session_id', 'user_message', 'ai_response', 'status', 'error_message', 'created_at'])
            ->map(fn ($c) => [
                'id'           => $c->id,
                'session_id'   => $c->session_id,
                'user_message' => $c->user_message,
                'ai_response'  => $c->ai_response,
                'status'       => $c->status,
                'error_message'=> $c->error_message,
                'created_at'   => $c->created_at->format('M d, Y H:i'),
            ]);

        return response()->json([
            'success'    => true,
            'session_id' => $conversation->session_id,
            'data'       => $messages,
        ]);
    }

    public function destroy($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);

        AiConversation::where('user_id', Auth::id())
            ->where('session_id', $conversation->session_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Conversation deleted successfully.']);
    }
}
