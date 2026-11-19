<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class PlantController extends Controller
{
    private const CATEGORIES = ['Ornamental', 'Flowering', 'Succulent', 'Herb'];

    public function index(): View
    {
        return view('plants.index', [
            'plants' => Plant::with('user')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('plants.create', ['categories' => self::CATEGORIES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($image instanceof UploadedFile) {
            $data['image_path'] = $this->storeImage($image);
        }

        $plant = $request->user()->plants()->create($data);

        return redirect()->route('plants.show', $plant)->with('success', 'Plant added to inventory.');
    }

    public function show(Plant $plant): View
    {
        return view('plants.show', ['plant' => $plant->load('user')]);
    }

    public function edit(Plant $plant): View
    {
        return view('plants.edit', ['plant' => $plant, 'categories' => self::CATEGORIES]);
    }

    public function update(Request $request, Plant $plant): RedirectResponse
    {
        $data = $this->validatedData($request);
        $image = $data['image'] ?? null;
        unset($data['image']);
        $oldImage = $plant->image_path;

        if ($image instanceof UploadedFile) {
            $data['image_path'] = $this->storeImage($image);
        }

        $plant->update($data);

        if ($image instanceof UploadedFile && $oldImage) {
            $this->deleteImage($oldImage);
        }

        return redirect()->route('plants.show', $plant)->with('success', 'Plant details updated.');
    }

    public function destroy(Plant $plant): RedirectResponse
    {
        $image = $plant->image_path;
        $plant->delete();

        if ($image) {
            $this->deleteImage($image);
        }

        return redirect()->route('plants.index')->with('success', 'Plant removed from inventory.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:'.implode(',', self::CATEGORIES)],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['required', 'string', 'max:5000'],
            'stock_status' => ['required', 'in:in_stock,out_of_stock'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('plants', 'public');

        if (! $path) {
            throw new RuntimeException('The plant image could not be saved.');
        }

        return $path;
    }

    private function deleteImage(string $path): void
    {
        $disk = Storage::disk('public');

        if ($disk->exists($path) && ! $disk->delete($path)) {
            throw new RuntimeException('The old plant image could not be removed.');
        }
    }
}
