<div class="card p-4">
    <h3 class="mb-4">Gaji & Tunjangan</h3>
    <form action="{{ route('job.wage.allowance.store', ['jobEmploymentId' => $jobEmploymentId]) }}" method="POST">
        @csrf
        <div id="wage-allowance-container">
            @foreach ($jobWageAllowance as $index => $allowance)
                <div class="wage-allowance-entry" id="wage_allowance_{{ $index }}">
                    <div class="row mb-3">
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                            <label for="type_{{ $index }}" class="form-label">Type</label>
                            <input type="text" class="form-control" id="type_{{ $index }}" name="type[]"
                                value="{{ old('type.' . $index, $allowance['type']) }}"
                                @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) disabled @endif>
                            @error('type.' . $index)
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                            <label for="amount_{{ $index }}" class="form-label">Amount</label>
                            @php
                                $amountValue = old('amount.' . $index, $allowance['amount']);
                                $amountValue = is_numeric($amountValue) ? (float) $amountValue : 0;
                            @endphp

                            <input type="text" class="form-control amount-input" id="amount_{{ $index }}"
                                name="amount[]" value="{{ number_format($amountValue, 0, ',', '.') }}"
                                @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) disabled @endif>

                            @error('amount.' . $index)
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                            <label for="calculation_{{ $index }}" class="form-label">Calculation</label>
                            <select class="form-select" id="calculation_{{ $index }}" name="calculation[]"
                                @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) disabled @endif>
                                <option value="Per Hari"
                                    {{ old('calculation.' . $index, $allowance['calculation']) == 'Per Hari' ? 'selected' : '' }}>
                                    Per Hari</option>
                                <option value="Per Bulan"
                                    {{ old('calculation.' . $index, $allowance['calculation']) == 'Per Bulan' ? 'selected' : '' }}>
                                    Per Bulan</option>
                            </select>
                            @error('calculation.' . $index)
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                            <label for="status_{{ $index }}" class="form-label">Status</label>
                            <select class="form-select" id="status_{{ $index }}" name="status[]"
                                @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3'])) disabled @endif>
                                <option value="Gross"
                                    {{ old('status.' . $index, $allowance['status']) == 'Gross' ? 'selected' : '' }}>
                                    Gross
                                </option>
                                <option value="Net"
                                    {{ old('status.' . $index, $allowance['status']) == 'Net' ? 'selected' : '' }}>Net
                                </option>
                            </select>
                            @error('status.' . $index)
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (Auth::user()->getRole() === 'admin')
            <button type="button" id="remove-wage-allowance" class="btn btn-danger mb-3 me-2"
                style="display: none;">Remove
                Wage/Allowance</button>
            <button type="button" id="add-wage-allowance" class="btn btn-primary mb-3">Add Wage/Allowance</button><br>
            <button type="submit" class="btn btn-success mb-3">Save</button>
        @endif

    </form>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let wageAllowanceCount = {{ $jobWageAllowance->count() }};
            const maxWageAllowance = 10; // Maximum allowed entries
            const wageAllowanceContainer = document.getElementById("wage-allowance-container");

            document.getElementById("add-wage-allowance").addEventListener("click", function() {
                if (wageAllowanceCount < maxWageAllowance) {
                    let newEntry = document.getElementById("wage_allowance_0").cloneNode(true);
                    newEntry.id = "wage_allowance_" + wageAllowanceCount;

                    // Update input names and clear values
                    let inputs = newEntry.querySelectorAll("input, select");
                    inputs.forEach(input => {
                        input.name = input.name.replace(/\[\d+\]/, `[${wageAllowanceCount}]`);
                        input.value = ""; // Clear input values
                    });

                    wageAllowanceContainer.appendChild(newEntry);
                    wageAllowanceCount++;

                    document.getElementById("remove-wage-allowance").style.display = "inline-block";
                    if (wageAllowanceCount === maxWageAllowance) this.style.display = "none";
                }
            });

            document.getElementById("remove-wage-allowance").addEventListener("click", function() {
                if (wageAllowanceCount > 3) {
                    wageAllowanceContainer.lastChild.remove();
                    wageAllowanceCount--;

                    document.getElementById("add-wage-allowance").style.display = "inline-block";
                    if (wageAllowanceCount === 3) this.style.display = "none";
                }
            });
        });
    </script>
    <script>
        document.querySelectorAll('.amount-input').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\./g, '').replace(/\D/g, '');
                e.target.value = new Intl.NumberFormat('id-ID').format(value);
            });
        });
    </script>
@endpush
