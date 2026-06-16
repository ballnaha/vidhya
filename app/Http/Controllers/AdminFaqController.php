<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminFaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'faqs' => $this->faqs($request->string('search')->toString()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $faq = Faq::create($validated);

        return response()->json([
            'message' => __('FAQ created.'),
            'faq' => $this->serializeFaq($faq),
        ], 201);
    }

    public function update(Request $request, Faq $faq): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $faq->update($validated);

        return response()->json([
            'message' => __('FAQ updated.'),
            'faq' => $this->serializeFaq($faq),
        ]);
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json([
            'message' => __('FAQ deleted.'),
        ]);
    }

    private function faqs(string $search = ''): array
    {
        return Faq::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('category', 'like', "%{$search}%")
                        ->orWhere('question', 'like', "%{$search}%")
                        ->orWhere('answer', 'like', "%{$search}%")
                        ->orWhere('keywords', 'like', "%{$search}%");
                });
            })
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Faq $faq) => $this->serializeFaq($faq))
            ->all();
    }

    private function serializeFaq(Faq $faq): array
    {
        return [
            'id' => $faq->id,
            'category' => $faq->category,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'keywords' => $faq->keywords,
            'sort_order' => $faq->sort_order,
            'created_at' => $faq->created_at?->format('M j, Y'),
        ];
    }
}
