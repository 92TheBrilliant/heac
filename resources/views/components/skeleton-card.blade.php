@props(['count' => 3])

@for($i = 0; $i < $count; $i++)
<div class="card animate-pulse">
    <div class="skeleton h-48 rounded-t-xl"></div>
    <div class="card-body">
        <div class="skeleton-title"></div>
        <div class="skeleton-text"></div>
        <div class="skeleton-text w-2/3"></div>
        <div class="flex items-center justify-between mt-4">
            <div class="skeleton h-4 w-20"></div>
            <div class="skeleton h-4 w-16"></div>
        </div>
    </div>
</div>
@endfor
