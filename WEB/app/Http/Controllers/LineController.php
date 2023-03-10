<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Line;
use Illuminate\Support\Facades\DB;
use App\Models\Stop;
use Illuminate\Support\Facades\Log;

class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lines = Line::all();
        $this->_navLog(__FUNCTION__); return view('line.index', ['lines' => $lines]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->_navLog(__FUNCTION__); return view('line.create', ['stops' => Stop::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $line = new Line();

        $line->name = $request->name;
        $line->destination_A = $request->A;
        $line->destination_B = $request->B;
        $line->save();

        return redirect()->route('lines.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $line = Line::where('id', $id)->first();
        $stops = Stop::all();
        $matching = DB::table('line_has_stops')->where('line_id', $id)->get();
        $this->_navLog(__FUNCTION__); return view('line.edit', ['line' => $line, 'stops' => $stops, 'matching' => $matching]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::table('line_has_stops')->where('line_id', $id)->delete();

        if($request->stop == null) {return redirect()->route('lines.index');};
        foreach($request->stop as $stop) {
            DB::insert('insert into line_has_stops (line_id, stop_id) values (?, ?)', [intval($id), intval($stop)]);
        }
        return redirect()->route('lines.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function _navLog($data) {
        Log::channel('UserNavigationActivity')->info(\Auth::user()->name.'('.\Auth::id().', '.\Auth::user()->roles[0]->name.') accessed function '.static::class.'::'.$data);
    }
}
