<?php

namespace App\Http\Controllers;
use App\Models\Scenario;
use App\Models\TestSuit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    /**
     * Display a listing of the user's scenarios.
     *
     * @return \Illuminate\View\View
     */
    
    public function index()
    {
        $scenarios = Scenario::all();
        $suits = TestSuit::all();

        return view('myscenarios.index', compact('scenarios', 'suits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'steps'       => 'required|array|min:1',
            'steps.*'     => 'required|string',
            'screenshot.*'=> 'nullable|image|mimes:jpg,jpeg,png',
            'video.*'     => 'nullable|mimetypes:video/mp4,video/webm',
        ]);

        $screenshots = [];
        $videos = [];

        if ($request->hasFile('screenshot')) {
            foreach ($request->file('screenshot') as $image) {
                $screenshots[] = $image->store('screenshots', 'public');
            }
        }

        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $vid) {
                $videos[] = $vid->store('videos', 'public');
            }
        }

        // Array'den boş değerleri temizle
        $steps = array_filter($request->steps, function($step) {
            return !empty(trim($step));
        });

        $scenario = new Scenario();
        $scenario->title = $request->title;
        $scenario->description = $request->description;
        $scenario->steps = array_values($steps); // Array'i yeniden indeksle
        $scenario->screenshots = $screenshots;
        $scenario->videos = $videos;
        $scenario->save();

        return redirect()->route('scenarios.index')->with('success', 'Senaryo başarıyla oluşturuldu.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $scenario = Scenario::findOrFail($id);
        $scenario->title = $request->title;
        $scenario->description = $request->description;
        $scenario->save();

        return redirect()->route('scenarios.index')->with('success', 'Senaryo güncellendi.');
    }

    public function destroy($id)
    {
        $scenario = Scenario::findOrFail($id);

        // İsteğe bağlı: Görsel ve videoları storage'dan silmek istersen
        if ($scenario->screenshots) {
            foreach ($scenario->screenshots as $img) {
                Storage::disk('public')->delete($img);
            }
        }
        if ($scenario->videos) {
            foreach ($scenario->videos as $vid) {
                Storage::disk('public')->delete($vid);
            }
        }

        $scenario->delete();
        return redirect()->route('scenarios.index')->with('success', 'Senaryo silindi.');
    }

    public function resetResults($id)
    {
        $scenario = Scenario::findOrFail($id);
        
        // Session'dan bu senaryonun sonuçlarını temizle
        $regressionResults = session('regression_results', []);
        if (isset($regressionResults[$id])) {
            unset($regressionResults[$id]);
            session(['regression_results' => $regressionResults]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Senaryo sonuçları sıfırlandı.'
        ]);
    }

    public function resetSingleResult(Request $request, $id)
    {
        $request->validate([
            'position' => 'required|integer|min:1|max:20'
        ]);

        $scenario = Scenario::findOrFail($id);
        $position = $request->position;
        
        // Session'dan bu senaryonun belirtilen pozisyonundaki sonucu temizle
        $regressionResults = session('regression_results', []);
        if (isset($regressionResults[$id])) {
            // Eğer array değilse, array yap
            if (!is_array($regressionResults[$id])) {
                $regressionResults[$id] = [];
            } else {
                $regressionResults[$id] = array_filter($regressionResults[$id], function($result) use ($position) {
                    return isset($result['position']) && $result['position'] != $position;
                });
                
                // Array'i yeniden indeksle
                $regressionResults[$id] = array_values($regressionResults[$id]);
            }
            
            // Eğer array boşsa tamamen kaldır
            if (empty($regressionResults[$id])) {
                unset($regressionResults[$id]);
            }
            
            session(['regression_results' => $regressionResults]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kutucuk sıfırlandı.'
        ]);
    }
}
