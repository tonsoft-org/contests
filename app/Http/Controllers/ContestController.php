<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContestResource;
use App\Models\Contender;
use App\Models\ContenderContest;
use App\Models\Contest;
use App\Models\ContestJury;
use App\Models\Jury;
use App\Models\Submission;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContestController extends Controller
{
    public function count()
    {
        return response([
            'data' => Contest::count(),
        ]);
    }

    public function index(Request $request)
    {
        $contests = Contest::orderBy('last_paid', 'desc');

        if (!is_null($request->get('id'))) {
            $contests = $contests->where('id', $request->get('id'));
        }

        if (!is_null($request->get('last_paid_gt'))) {
            $contests = $contests->where('last_paid', '>', $request->get('last_paid_gt'));
        }

        if (!is_null($request->get('last_paid_lt'))) {
            $contests = $contests->where('last_paid', '<', $request->get('last_paid_lt'));
        }

        if (!is_null($request->get('limit'))) {
            $contests = $contests->limit($request->get('limit'));
        } else {
            $contests = $contests->limit(10);
        }

        if (!is_null($request->get('with'))) {
            foreach ($request->get('with') as $with) {
                $contests->with($with);
            }
        }
        $contests = $contests->get();



        return response([
            'data' => ContestResource::collection($contests)
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $contest = json_decode($request->get('contest'), 1);

        $contest_validator = Validator::make($contest, [
            'id' => 'required|string',
            'balance' => 'required|numeric',
            'title' => 'required|string',
            'hash' => 'required|string',
            'last_paid' => 'required|integer',
            'link' => 'required|string',
            'max_voting_attempts' => 'required|integer',
            'jury_id' => 'required|string',
            'start_at' => 'required|integer',
            'end_at' => 'required|integer',
            'voting_end_at' => 'required|integer',

            'jury' => 'required|array',
            'juries' => 'required|array',
            'contenders' => 'present|array',
            'submissions' => 'present|array',
            'votes' => 'present|array',
        ]);
        if ($contest_validator->fails()) return $contest_validator->errors();

        $parameters = $contest_validator->valid();

        $contest = Contest::find($parameters['id']);
        if ($contest) {
            $contest->delete();
        }
        $contest = new Contest();
        $contest->fill($parameters)->save();

        $jury = Jury::find($parameters['jury_id']);
        if (!$jury) {
            $jury = (new Jury(['id' => $parameters['jury_id']]));
            $jury->save();
        }
        $contest->jury_id = $jury->id;

        foreach ($parameters['juries'] as $value) {
            $jury = Jury::find($value['id']);
            if (!$jury) {
                $jury = (new Jury(['id' => $value['id']]));
                $jury->save();
            }
            $contest_jury = ContestJury::where('jury_id', $jury->id)->where('contest_id', $contest->id)->first();
            if (!$contest_jury) {
                $contest->juries()->attach($jury);
            }
        }

        foreach ($parameters['contenders'] as $value) {
            $contender = Contender::find($value['id']);
            if (!$contender) {
                $contender = (new Contender(['id' => $value['id']]));
                $contender->save();
            }
            $contender_contest = ContenderContest::where('contender_id', $contender->id)->where('contest_id', $contest->id)->first();
            if (!$contender_contest) {
                $contest->contenders()->attach($contender);
            }
        }

        foreach ($parameters['submissions'] as $value) {
            $submission = (new Submission([
                'id' => $value['id'],
                'contest_id' => $contest->id,
                'address' => $value['address'],
                'contact' => $value['contact'],
                'applied_at' => $value['applied_at'],
                'file_link' => $value['file_link'],
                'forum_link' => $value['forum_link'],
                'hash' => $value['hash'],
                'total_mark' => $value['total_mark'],
                'avg_mark' => $value['avg_mark'],
                'accepted' => $value['accepted'],
                'abstained' => $value['abstained'],
                'rejected' => $value['rejected'],
            ]));
            $submission->save();
        }

        foreach ($parameters['votes'] as $value) {
            $check = Vote::where('submission_id', $value['submission_id'])
                ->where('contest_id', $contest->id)
                ->where('jury_id', $value['jury_id'])
                ->first();
            if ($check) { continue; }

            $vote = (new Vote([
                'submission_id' => $value['submission_id'],
                'contest_id' => $contest->id,
                'jury_id' => $value['jury_id'],
                'type' => $value['type'],
                'type_text' => $value['type_text'],
                'mark' => $value['mark'] ?? 0,
                'comment' => $value['comment'],
            ]));
            $vote->save();
        }

        DB::commit();

        return true;
    }
}
