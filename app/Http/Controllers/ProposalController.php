<?php

namespace App\Http\Controllers;

use App\Mail\ProposalMail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use PDF;
use App\Http\Requests\StoreProposalRequest;
use App\Jobs\SendMailProposal;
use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Proposal/ProposalIndex', [
            'proposals' => Proposal::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Proposal/ProposalCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProposalRequest $request)
    {
        $payload = $request->validated();

        DB::beginTransaction();

        try {
            $proposalData = [
                'customer_id'    => $payload['customer']['id'],
                'customer_title' => $payload['customer']['title'],
                'customer_email' => $payload['customer']['email'],
                'total'          => 0
            ];

            // Calc total price
            foreach ($payload['cars'] as $line) {
                $proposalData['total'] += $line['quantity'] * $line['price'];
            }

            // Save document
            $proposal = Proposal::create($proposalData);

            // Save lines
            $proposalLine = [];

            foreach ($payload['cars'] as $line) {
                $proposalLine[] = [
                    'proposal_id' => $proposal->id,
                    'car_id'      => $line['id'],
                    'title'       => $line['title'],
                    'quantity'    => $line['quantity'],
                    'price'       => $line['price'],
                    'total'       => $line['quantity'] * $line['price']
                ];
            }

            // Save document lines
            ProposalItem::insert($proposalLine);

            // Create PDF
            $fullPath = $this->savePdfLocation($proposal->id) . "/Proposal_{$proposal->id}.pdf";

            $pdf = PDF::loadView('pdf.proposal', ['proposal' => $proposal]);
            $pdf->save($fullPath);

            $proposal->update([
                'pdf_link' => str_replace(public_path(), '', $fullPath)
            ]);

            DB::commit();

            // Send Mail
            dispatch(function () use ($proposal) {
                SendMailProposal::dispatch($proposal);
            })->afterResponse();


            return response()->json($proposal, 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function show(Proposal $proposal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function edit(Proposal $proposal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proposal $proposal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Proposal $proposal
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Proposal $proposal)
    {
        DB::beginTransaction();

        $pdfPath = public_path($proposal->pdf_file);

        try {
            ProposalItem::query()
                ->where('proposal_id', $proposal->id)
                ->delete();

            $proposal->delete();

            DB::commit();

            if (file_exists($pdfPath)) {
                File::delete($pdfPath);
            }

        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ], 400);
        }

        return response()->noContent(200);
    }

    /**
     * Kaydedilecek pdf dosyasının dosya yolunu oluşturur
     *
     * @param $id
     * @return string
     */
    protected function savePdfLocation($id)
    {
        // Create main folder
        if (!file_exists(public_path('documents'))) {
            mkdir(public_path('documents'));
        }

        // Create year folder
        if (!file_exists(public_path('documents/' . date('Y')))) {
            mkdir(public_path('documents/' . date('Y')));
        }

        // Create month folder
        if (!file_exists(public_path('documents/' . date('Y/m')))) {
            mkdir(public_path('documents/' . date('Y/m')));
        }

        // Create day folder
        if (!file_exists(public_path('documents/' . date('Y/m/d')))) {
            mkdir(public_path('documents/' . date('Y/m/d')));
        }

        // Create record folder
        if (!file_exists(public_path('documents/' . date('Y/m/d') . "/P{$id}"))) {
            mkdir(public_path('documents/' . date('Y/m/d') . "/P{$id}"));
        }

        // return full path
        return public_path('documents/' . date('Y/m/d') . "/P{$id}");
    }
}
