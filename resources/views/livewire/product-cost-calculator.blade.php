<div class="card p-4 shadow-sm">
    <h3 class="mb-4">🧮 Calculadora de Costo de Producción</h3>

    <div class="mb-3">
        <label for="product" class="form-label">Selecciona un producto:</label>
        <select wire:change="calculate" wire:model="selectedProductId" class="form-select">
            <option value="">-- Elige un producto --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->presentation_ml }}ml)</option>
            @endforeach
        </select>
    </div>

    @if($error)
        <div class="alert alert-danger mt-3">
            {{ $error }}
        </div>
    @endif

    @if($costResult)
        <div class="mt-4 p-3 bg-light rounded">
            <h5>📊 Resultado para: <strong>{{ $costResult['product_name'] }}</strong></h5>
            <ul class="list-group list-group-flush mt-2">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Materiales directos:</span>
                    <strong>${{ number_format($costResult['direct_materials'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Empaque:</span>
                    <strong>${{ number_format($costResult['packaging'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Suministros (agua, luz, gas):</span>
                    <strong>${{ number_format($costResult['supplies'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Mano de obra:</span>
                    <strong>${{ number_format($costResult['labor'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-info text-white">
                    <span><strong>Subtotal antes de gastos:</strong></span>
                    <strong>${{ number_format($costResult['subtotal_before_overheads'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Gastos indirectos:</span>
                    <strong>${{ number_format($costResult['overheads'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Utilidad deseada:</span>
                    <strong>${{ number_format($costResult['profit_margin'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-success text-white">
                    <span><strong>Total costo lote:</strong></span>
                    <strong>${{ number_format($costResult['total_cost'], 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-primary text-white">
                    <span><strong>Costo unitario:</strong></span>
                    <strong>${{ number_format($costResult['unit_cost'], 2) }}</strong>
                </li>
            </ul>

            <p class="mt-2 text-muted">
                Lote: {{ $costResult['units_per_batch'] }} unidades de {{ $costResult['presentation_ml'] }}ml
            </p>
        </div>
    @endif
</div>