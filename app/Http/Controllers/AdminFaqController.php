<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminFaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'faqs' => $this->faqs($request->string('search')->toString()),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('faqs', 'sort_order')],
        ]);

        $faq = DB::transaction(function () use ($validated) {
            $faq = Faq::create($validated);
            $this->normalizeFaqOrder();

            return $faq->refresh();
        });

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
            'sort_order' => ['required', 'integer', 'min:0', Rule::unique('faqs', 'sort_order')->ignore($faq)],
        ]);

        $faq = DB::transaction(function () use ($faq, $validated) {
            $faq->update($validated);
            $this->normalizeFaqOrder();

            return $faq->refresh();
        });

        return response()->json([
            'message' => __('FAQ updated.'),
            'faq' => $this->serializeFaq($faq),
        ]);
    }

    public function destroy(Faq $faq): JsonResponse
    {
        DB::transaction(function () use ($faq) {
            $faq->delete();
            $this->normalizeFaqOrder();
        });

        return response()->json([
            'message' => __('FAQ deleted.'),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:faqs,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $sortSlots = Faq::query()
                ->whereIn('id', $validated['ids'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('sort_order')
                ->all();

            foreach ($validated['ids'] as $id) {
                Faq::whereKey($id)->update([
                    'sort_order' => -$id,
                ]);
            }

            foreach ($validated['ids'] as $index => $id) {
                Faq::whereKey($id)->update([
                    'sort_order' => $sortSlots[$index],
                ]);
            }
        });

        return response()->json([
            'message' => __('FAQ order updated.'),
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
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Faq $faq) => $this->serializeFaq($faq))
            ->all();
    }

    private function normalizeFaqOrder(): void
    {
        $ids = Faq::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        foreach ($ids as $id) {
            Faq::whereKey($id)->update([
                'sort_order' => -$id,
            ]);
        }

        collect($ids)
            ->each(function (int $id, int $index) {
                Faq::whereKey($id)->update([
                    'sort_order' => ($index + 1) * 10,
                ]);
            });
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
