<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.head')
</head>

<body class="h-screen bg-white dark:bg-zinc-800 flex">

  <aside class="h-full w-64 shrink-0">
    <livewire:seller.layouts.sidebar />
  </aside>

  <main class="min-w-0 flex-1 overflow-y-auto">
    {{$slot}}
  </main>
  @livewireScripts
</body>

</html>
