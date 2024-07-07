<div class="flex flex-col bg-base-100 p-2 rounded-lg px-4">
    <div class="text-sm text-neutral dark:text-gray-400 uppercase">
        Welcome,
    </div>
    <div class="text-xl text-primary font-bold uppercase">
        {{ auth()->user()->name }}!
    </div>
    <div class="text-sm text-secondary font-semibold uppercase italic text-right">
        {{ auth()->user()->title }}
    </div>
</div>
