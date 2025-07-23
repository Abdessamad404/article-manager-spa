<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    private $categories = [
        'Économie',
        'Industrie',
        'Innovation',
        'Politique',
        'Technology'
    ];

    public function index(Request $request)
    {
        $query = Article::with('author');

        // Filtres
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
            });
        }

        // Autorisation selon le rôle
        // if (Auth::user()->hasRole('writer')) {
        //     $query->where('author_id', Auth::id());
        // }

        $articles = $query->latest()->paginate(10);

        return response()->json([
            'articles' => $articles,
            'categories' => $this->categories,
            'filters' => $request->only(['category', 'status', 'author']) // Filtres actuellement appliqués
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category' => ['required', Rule::in($this->categories)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $article = Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'image' => $imagePath,
            'author_id' => Auth::id(),
            'status' => 'draft'
        ]);

        return response()->json([
            'message' => 'Article créé avec succès!',
            'article' => $article->load('author')
        ], 201);
    }

    public function show(Article $article)
    {
        return response()->json([
            'article' => $article->load('author')
        ]);
    }

    public function update(Request $request, Article $article)
    {
        // Vérifier l'autorisation
        if (Auth::user()->hasRole('writer') && $article->author_id !== Auth::id() && !Auth::user()->hasRole('editor')) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:10',
            'category' => ['required', Rule::in($this->categories)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $article->update($validated);

        return response()->json([
            'message' => 'Article mis à jour avec succès!',
            'article' => $article->load('author')
        ]);
    }

    public function destroy(Article $article)
    {
        if (Auth::user()->hasRole('writer') && $article->author_id !== Auth::id() && !Auth::user()->hasRole('editor')) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($article->status !== 'draft') {
            return response()->json([
                'message' => 'Seuls les articles en brouillon peuvent être supprimés.'
            ], 400);
        }

        $article->delete();

        return response()->json([
            'message' => 'Article supprimé avec succès!'
        ]);
    }

    public function submit(Article $article)
    {
        if (Auth::user()->hasRole('writer') && $article->author_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($article->status !== 'draft') {
            return response()->json([
                'message' => 'Seuls les articles en brouillon peuvent être soumis.'
            ], 400);
        }

        $article->update(['status' => 'pending']);

        return response()->json([
            'message' => 'Article soumis pour validation!',
            'article' => $article->load('author')
        ]);
    }

    public function approve(Article $article)
    {
        if (!Auth::user()->hasRole('editor')) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($article->status !== 'pending') {
            return response()->json([
                'message' => 'Seuls les articles en attente peuvent être approuvés.'
            ], 400);
        }

        $article->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Article approuvé avec succès!',
            'article' => $article->load('author')
        ]);
    }

    public function reject(Article $article)
    {
        if (!Auth::user()->hasRole('editor')) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        if ($article->status !== 'pending') {
            return response()->json([
                'message' => 'Seuls les articles en attente peuvent être rejetés.'
            ], 400);
        }

        $article->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Article rejeté.',
            'article' => $article->load('author')
        ]);
    }

    public function publicIndex(Request $request)
    {
        $query = Article::with('author')->where('status', 'approved');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $sortBy = $request->get('sort', 'date');
        $direction = $request->get('direction', 'desc');

        if ($sortBy === 'category') {
            $query->orderBy('category', $direction)->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', $direction);
        }

        $articles = $query->paginate(12);

        return response()->json([
            'articles' => $articles,
            'categories' => $this->categories,
            'filters' => $request->only(['category']),
            'sort' => $sortBy,
            'direction' => $direction
        ]);
    }
}