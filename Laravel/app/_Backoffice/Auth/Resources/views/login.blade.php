<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · caudexbid</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-stone-50 font-sans text-stone-800 antialiased">
    <div class="w-full max-w-sm px-4">
        <div class="mb-6 flex items-center gap-3">
            <span class="flex size-10 items-center justify-center rounded-xl bg-brand-600 font-bold text-white">cb</span>
            <div>
                <p class="font-semibold text-stone-900">caudexbid</p>
                <p class="text-sm text-stone-500">Back office</p>
            </div>
        </div>

        <form method="POST" action="{{ route('bo.login.attempt') }}" class="space-y-4 rounded-xl border border-stone-200 bg-white p-6">
            @csrf
            @if ($errors->has('business'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first('business') }}</div>
            @endif
            <x-bo.field name="identifiant" label="Identifiant" autofocus autocomplete="username" required />
            <x-bo.field name="password" label="Mot de passe" type="password" autocomplete="current-password" required />
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-stone-300"> Rester connecté
            </label>
            <button class="btn w-full justify-center">Se connecter</button>
        </form>
    </div>
</body>
</html>
