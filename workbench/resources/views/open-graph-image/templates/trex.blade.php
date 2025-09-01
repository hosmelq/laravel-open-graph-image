<x-open-graph-image-layout>
    <div class="flex items-center justify-center p-8 h-screen bg-[#FEFCF7]">
        <div class="w-1/2 flex items-center justify-center">
            <img
                class="max-w-[500px]"
                src="@embedImage(public_path('images/trex.png'))"
            >
        </div>
        <div class="flex w-1/2 flex-col gap-y-4 p-8">
            <div class="flex gap-x-2">
                @foreach(['animals', 'extinct', 'predator'] as $tag)
                    <div class="rounded-md border px-2 py-0.5 text-xs border-black/30 bg-black/10 text-black/80">
                        {{ $tag }}
                    </div>
                @endforeach
            </div>
            <h1 class="font-bold text-4xl">Tyrannosaurus Rex</h1>
            <p class="text-black/80 text-lg">
                The Tyrannosaurus Rex was a massive, carnivorous dinosaur that
                lived
                during the late Cretaceous period. Known for its powerful jaws,
                sharp
                teeth, and tiny arms, it is one of the most famous and fearsome
                prehistoric predators.
            </p>
        </div>
    </div>
</x-open-graph-image-layout>
