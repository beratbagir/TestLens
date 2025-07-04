<?php

namespace App\Http\Controllers;
use App\Models\TestSuit;
use Illuminate\Http\Request;
use App\Models\Scenario;
class TestSuitController extends Controller
{
    public function index()
{
    $scenarios = Scenario::all();
    $suits = TestSuit::all();

    return view('myscenarios.index', compact('scenarios', 'suits'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'scenario_ids' => 'required|array|min:2'
        ]);

        TestSuit::create([
            'name' => $request->name,
            'scenario_ids' => $request->scenario_ids
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'scenario_ids' => 'array'
        ]);

        $suit = TestSuit::findOrFail($id);
        
        $suit->update([
            'name' => $request->name,
            'scenario_ids' => $request->scenario_ids ?? []
        ]);

        return redirect()->route('scenarios.index')->with('success', 'Test suit başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $suit = TestSuit::findOrFail($id);
        $suit->delete();

        return redirect()->route('scenarios.index')->with('success', 'Test suit başarıyla silindi.');
    }

    public function removeScenario(Request $request, $id)
    {
        $request->validate([
            'scenario_id' => 'required|integer'
        ]);

        $suit = TestSuit::findOrFail($id);
        $scenarioIds = $suit->scenario_ids;
        
        // Senaryoyu listeden çıkar
        $scenarioIds = array_filter($scenarioIds, function($scenarioId) use ($request) {
            return $scenarioId != $request->scenario_id;
        });

        // Array'i yeniden indeksle
        $scenarioIds = array_values($scenarioIds);

        $suit->update([
            'scenario_ids' => $scenarioIds
        ]);

        return response()->json(['success' => true, 'message' => 'Senaryo test suitinden çıkarıldı.']);
    }
}