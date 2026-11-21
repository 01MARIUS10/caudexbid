<x-bo.layout title="Liste des plantes">
    @php
        $list = $plants->toArray();
        $filtered = request()->filled('q') || request()->filled('genre');
        $chip = fn (bool $active) => $active
            ? 'border-brand-600 bg-brand-50 font-medium text-brand-900'
            : 'border-stone-200 bg-white text-stone-600 hover:border-stone-300 hover:text-stone-900';
        $leaf = '<path d="M6.5 21c-1.6-3.6.6-7 5.5-7s7.1 3.4 5.5 7Z"/><path d="M12 14V8"/><path d="M12 10.5c-2-.6-3.4-2.3-3.6-4.5"/><path d="M12 9c1.8-.4 3.2-1.8 3.8-4"/>';
    @endphp

    {{-- En-tête : rôle de la collection + action principale. --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <p class="max-w-2xl text-sm text-stone-500">
            Information générale des plantes. Lors de la création d'un <a class="link" href="{{ route('bo.products.create') }}">lot</a>,
            choisir une plante pré-remplit le nom (modifiable avant d'enregistrer).
        </p>
        <button type="button" class="btn" onclick="document.getElementById('plant-dialog').showModal()">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Ajouter une plante
        </button>
    </div>

    {{-- Recherche + filtre par genre (pastilles). --}}
    <div class="space-y-3">
        <form class="flex flex-wrap items-center gap-2" role="search">
            @if (request('genre'))<input type="hidden" name="genre" value="{{ request('genre') }}">@endif
            <div class="relative w-full sm:w-72">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un nom ou un genre…" aria-label="Rechercher" class="input pl-9">
            </div>
            <button class="btn-secondary">Rechercher</button>
            @if ($filtered)
                <a href="{{ route('bo.plants.index') }}" class="text-sm text-stone-500 hover:text-stone-900">Réinitialiser</a>
            @endif
            <span class="ml-auto text-sm text-stone-500 tabular-nums">{{ count($list) }} plante{{ count($list) > 1 ? 's' : '' }}</span>
        </form>

        @if ($genres)
            <nav class="-mx-1 flex gap-1.5 overflow-x-auto px-1 pb-1" aria-label="Filtrer par genre">
                <a href="{{ route('bo.plants.index', array_filter(['q' => request('q')])) }}" @class(['shrink-0 rounded-full border px-3 py-1 text-sm transition', $chip(! request('genre'))])>Tous les genres</a>
                @foreach ($genres as $g)
                    <a href="{{ route('bo.plants.index', array_filter(['q' => request('q'), 'genre' => $g])) }}"
                       @if (request('genre') === $g) aria-current="true" @endif
                       @class(['shrink-0 rounded-full border px-3 py-1 text-sm italic transition', $chip(request('genre') === $g)])>{{ $g }}</a>
                @endforeach
            </nav>
        @endif
    </div>

    {{-- Collection en cartes. --}}
    @if ($list)
        <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            @foreach ($list as $p)
                <li class="group flex flex-col overflow-hidden rounded-xl border border-stone-200 bg-white transition hover:border-stone-300 hover:shadow-sm">
                    {{-- Image : identité visuelle de la collection uniquement. Clic = ajouter / remplacer. --}}
                    <form method="POST" action="{{ route('bo.plants.image', $p['id']) }}" enctype="multipart/form-data">
                        @csrf
                        <label class="relative block aspect-[4/3] cursor-pointer overflow-hidden bg-stone-100">
                            @if ($p['image_url'])
                                <img src="{{ $p['image_url'] }}" alt="{{ $p['nom'] }}" loading="lazy" decoding="async"
                                     class="size-full object-cover transition duration-500 group-hover:scale-[1.03]">
                            @else
                                <span class="flex size-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-brand-50 to-stone-100 text-stone-400">
                                    <svg viewBox="0 0 24 24" class="size-9 text-brand-600/40" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $leaf !!}</svg>
                                    <span class="text-xs">Ajouter une image</span>
                                </span>
                            @endif
                            <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1.5 bg-gradient-to-t from-stone-900/70 to-transparent px-3 pt-8 pb-2.5 text-xs font-medium text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                                <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                                {{ $p['image_url'] ? 'Changer l\'image' : 'Choisir une image' }}
                            </span>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only"
                                   aria-label="Image de {{ $p['nom'] }}" onchange="this.form.submit()">
                        </label>
                    </form>

                    <div class="flex flex-1 flex-col gap-3 p-4">
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold tracking-widest text-brand-700 uppercase">{{ $p['genre'] ?? 'Genre non renseigné' }}</p>
                            <h3 class="mt-0.5 truncate text-[15px] font-semibold text-stone-900 italic" title="{{ $p['nom'] }}">{{ $p['nom'] }}</h3>
                        </div>

                        <div class="mt-auto flex items-center justify-between gap-3 border-t border-stone-100 pt-3">
                            @if ($p['products_count'])
                                <a href="{{ route('bo.products.index', ['plante' => $p['id']]) }}" class="flex items-center gap-1.5 text-sm text-stone-600 hover:text-brand-700" title="Voir les lots de cette plante">
                                    <svg viewBox="0 0 24 24" class="size-4 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m14.5 12.5-8 8a2.12 2.12 0 1 1-3-3l8-8"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/></svg>
                                    <span><span class="font-semibold tabular-nums">{{ $p['products_count'] }}</span> lot{{ $p['products_count'] > 1 ? 's' : '' }}</span>
                                </a>
                            @else
                                <span class="text-sm text-stone-400">Aucun lot</span>
                            @endif
                            <a href="{{ route('bo.products.create', ['plante' => $p['id']]) }}" class="btn-secondary px-2.5 py-1.5 text-xs">
                                <svg viewBox="0 0 24 24" class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                Créer un lot
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-stone-300 bg-white px-6 py-14 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-brand-50">
                <svg viewBox="0 0 24 24" class="size-6 text-brand-700" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $leaf !!}</svg>
            </span>
            @if ($filtered)
                <p class="text-sm font-medium text-stone-900">Aucune plante ne correspond à cette recherche</p>
                <a href="{{ route('bo.plants.index') }}" class="link text-sm">Voir toute la collection</a>
            @else
                <p class="text-sm font-medium text-stone-900">La collection est vide</p>
                <p class="max-w-sm text-sm text-stone-500">Ajoutez une plante pour pouvoir la choisir lors de la création d'un lot.</p>
                <button type="button" class="btn mt-1" onclick="document.getElementById('plant-dialog').showModal()">Ajouter une plante</button>
            @endif
        </div>
    @endif

    {{-- Ajout d'une plante : fenêtre modale (rouverte automatiquement en cas d'erreur de saisie). --}}
    <dialog id="plant-dialog" aria-labelledby="plant-dialog-title"
            class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl border border-stone-200 bg-white p-0 shadow-xl backdrop:bg-stone-900/40 backdrop:backdrop-blur-[2px]">
        <form method="POST" action="{{ route('bo.plants.store') }}" enctype="multipart/form-data">
            @csrf
            <header class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-4">
                <div>
                    <h2 id="plant-dialog-title" class="text-sm font-semibold text-stone-900">Ajouter une plante</h2>
                    <p class="text-xs text-stone-500">Propriétaire, descriptions, taille, prix et photos se saisissent sur le lot.</p>
                </div>
                <button type="button" class="rounded-md p-1 text-stone-400 hover:bg-stone-100 hover:text-stone-700" aria-label="Fermer" onclick="this.closest('dialog').close()">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </header>

            <div class="space-y-4 px-5 py-5">
                <x-bo.field name="nom" label="Nom de la plante *" required placeholder="Pachypodium gracilius" />
                <x-bo.field name="genre" label="Genre" placeholder="Pachypodium" list="plant-genres" autocomplete="off" />
                <datalist id="plant-genres">@foreach ($genres as $g)<option value="{{ $g }}">@endforeach</datalist>

                <div>
                    <span class="label">Image</span>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-stone-300 p-3 transition hover:border-brand-600 hover:bg-brand-50/40">
                        <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-md bg-stone-100" data-preview>
                            <svg viewBox="0 0 24 24" class="size-6 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                        </span>
                        <span class="min-w-0 text-sm">
                            <span class="block truncate font-medium text-stone-700" data-filename>Choisir une image</span>
                            <span class="block text-xs text-stone-500">Facultatif · JPG, PNG ou WebP, 10 Mo max</span>
                        </span>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    </label>
                    <p class="mt-1 text-xs text-stone-500">Identité visuelle de la collection uniquement, jamais affichée sur un lot.</p>
                    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <footer class="flex justify-end gap-2 rounded-b-xl border-t border-stone-100 bg-stone-50/60 px-5 py-3">
                <button type="button" class="btn-secondary" onclick="this.closest('dialog').close()">Annuler</button>
                <button class="btn">Ajouter à la collection</button>
            </footer>
        </form>
    </dialog>

    <script>
        (() => {
            const dialog = document.getElementById('plant-dialog');
            // Clic sur le fond : fermer.
            dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
            // Aperçu de l'image choisie.
            const input = dialog.querySelector('input[type=file]');
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) return;
                dialog.querySelector('[data-filename]').textContent = file.name;
                const img = new Image();
                img.className = 'size-full object-cover';
                img.src = URL.createObjectURL(file);
                dialog.querySelector('[data-preview]').replaceChildren(img);
            });
            @if ($errors->hasAny(['nom', 'genre', 'image']))
                dialog.showModal();
            @endif
        })();
    </script>
</x-bo.layout>
