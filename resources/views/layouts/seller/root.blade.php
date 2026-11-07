<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

  {{$slot}}
  @livewireScripts
</body>

</html>