@php
$minutesElapsed = (int) $order->created_at->diffInMinutes(now());

$isCompleted = $type === 'completed';
$isCancelled = $type === 'cancelled';
$isFinal = $isCompleted || $isCancelled;
$isOverdue = !$isFinal && $minutesElapsed >= 15;
$isWarning = !$isFinal && $minutesElapsed >= 10 && $minutesElapsed < 15;

    $cardClassType='order-card-' . $type;

    $borderClass='border-slate-600' ;
    if (!$isFinal) {
    if ($isOverdue) {
    $borderClass='order-overdue' ;
    } elseif ($isWarning) {
    $borderClass='border-yellow-500' ;
    } else {
    if ($type==='billing' ) $borderClass='border-red-500' ;
    elseif ($type==='cooking' ) $borderClass='border-blue-500' ;
    elseif ($type==='serving' ) $borderClass='border-emerald-500' ;
    }
    } elseif ($isCancelled) {
    $borderClass='border-red-500/50' ;
    }
    @endphp

    <div class="{{ $cardClassType }} relative bg-cardBg/90 backdrop-blur-sm rounded-2xl border-l-4 {{ $borderClass }} shadow-lg overflow-hidden transition-all duration-300 ring-1 ring-white/5 hover:ring-white/10 group {{ $isFinal ? 'opacity-60 grayscale-[30%]' : '' }}">

    <div class="p-3.5 border-b border-borderColor flex justify-between items-center bg-darkBg/60">
        <div class="flex items-center gap-3">
            <div class="bg-darkBg rounded-xl px-3 py-1.5 border {{ $order->order_type == 'take_away' ? 'border-primary/50 bg-primary/10' : 'border-borderColor' }} shadow-inner text-center min-w-[3.5rem]">
                @if($order->order_type == 'take_away')
                <p class="text-[9px] font-black text-primary uppercase tracking-widest mb-0.5">Tipe</p>
                <h3 class="text-sm font-black {{ $isFinal ? 'text-textDim' : 'text-primary' }} leading-none mt-1 mb-1">
                    <i class="fas fa-shopping-bag"></i>
                </h3>
                @else
                <p class="text-[9px] font-black text-textDim uppercase tracking-widest mb-0.5">Meja</p>
                <h3 class="text-2xl font-black {{ $isFinal ? 'text-textDim' : 'text-white' }} leading-none">{{ $order->table->table_number ?? '-' }}</h3>
                @endif
            </div>

            <div class="flex flex-col">
                <span class="text-[10px] font-mono text-textDim font-semibold uppercase tracking-wider">#{{ substr($order->transaction_id, -6) }}</span>
                <span class="text-xs font-black {{ $isFinal ? 'text-textDim' : 'text-primary' }} mt-0.5 flex items-center gap-1">
                    <i class="far fa-clock"></i> {{ $order->created_at->format('H:i') }}
                </span>
            </div>
        </div>

        <div class="text-right flex flex-col items-end justify-center">
            @if($isCancelled)
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-red-500/20 text-red-400 px-2 py-1 rounded-md border border-red-500/30">
                <i class="fas fa-ban"></i> DIBATALKAN
            </span>
            @elseif(!$isFinal)
            @if($isOverdue)
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-red-500/20 text-red-400 px-2 py-1 rounded-md border border-red-500/30">
                <i class="fas fa-exclamation-triangle"></i> Telat {{ $minutesElapsed }} mnt
            </span>
            @elseif($isWarning)
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-md border border-yellow-500/30">
                <i class="fas fa-hourglass-half"></i> {{ $order->created_at->diffForHumans(null, true) }}
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-secondary text-textDim px-2 py-1 rounded-md border border-borderColor">
                {{ $order->created_at->diffForHumans() }}
            </span>
            @endif
            @endif
        </div>
    </div>

    <div class="p-4 space-y-3">
        @foreach($order->items as $item)
        <div class="flex items-start gap-3 relative pb-2 border-b border-borderColor last:border-0 last:pb-0">
            <span class="bg-darkBg text-primary font-black px-2 py-1 rounded-lg text-xs border border-borderColor shadow-sm shrink-0">
                {{ $item->quantity }}x
            </span>

            <div class="flex-1 pt-0.5">
                <p class="text-sm font-bold {{ $isFinal ? 'text-textDim line-through' : 'text-slate-200' }} leading-tight">
                    {{ $item->menu->name }}
                </p>
                @if($item->notes)
                <div class="mt-1.5 flex items-start gap-1.5">
                    <i class="fas fa-comment-dots text-[10px] text-primary/80 mt-0.5"></i>
                    <p class="text-[11px] text-primary/90 font-medium italic leading-snug bg-primary/10 px-2 py-1 rounded border border-primary/20">
                        {{ $item->notes }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if(!$isFinal)
    <div class="p-2.5 bg-darkBg/80 flex gap-2 border-t border-borderColor">

        @if($type === 'billing')
        <div class="flex-1 flex gap-2">
            @if($order->payment_method == 'cash')
            <button type="button" onclick="openCashModal('{{ route('admin.order.confirmPayment', $order->id) }}', '{{ $order->total_price }}')" class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white text-xs font-black py-3.5 rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-red-500/20 active:scale-95 flex items-center justify-center">
                <i class="fas fa-check-circle mr-1.5 text-sm"></i> Lunas Tunai
            </button>
            @else
            <button type="button" disabled class="w-full bg-secondary text-textDim border border-borderColor text-xs font-black py-3.5 rounded-xl uppercase tracking-widest cursor-not-allowed flex items-center justify-center">
                <i class="fas fa-qrcode mr-1.5 text-sm"></i> Menunggu QRIS
            </button>
            @endif
        </div>

        @elseif($type === 'cooking')
        <form action="{{ route('order.status', $order->id) }}" method="POST" class="flex-1 kds-action-form">
            @csrf
            <input type="hidden" name="status" value="preparing">
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white text-xs font-black py-3.5 rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95 flex justify-center items-center">
                <i class="fas fa-fire mr-1.5 text-sm"></i> Masak
            </button>
        </form>

        @elseif($type === 'serving')
        <form action="{{ route('order.status', $order->id) }}" method="POST" class="flex-1 kds-action-form">
            @csrf
            <input type="hidden" name="status" value="served">

            @if($order->order_type == 'take_away')
            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white text-xs font-black py-3.5 rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-purple-500/20 active:scale-95 flex justify-center items-center" title="Pindahkan ke area Pick-up Kasir">
                <i class="fas fa-box-open mr-1.5 text-sm"></i> Siap Pick-up
            </button>
            @else
            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white text-xs font-black py-3.5 rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20 active:scale-95 flex justify-center items-center">
                <i class="fas fa-bell mr-1.5 text-sm"></i> Sajikan
            </button>
            @endif
        </form>
        @endif

        <button type="button" onclick="openCancelModal('{{ route('order.status', $order->id) }}', '{{ substr($order->transaction_id, -6) }}')" class="w-11 h-full bg-secondary hover:bg-red-500 hover:text-white text-textDim border border-borderColor hover:border-red-500 rounded-xl transition-all flex items-center justify-center group" title="Batalkan Pesanan">
            <i class="fas fa-trash-alt text-xs group-hover:scale-110 transition-transform"></i>
        </button>
    </div>
    @endif
    </div>