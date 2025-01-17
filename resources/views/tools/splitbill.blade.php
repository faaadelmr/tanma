<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-primary">{{ __('Split Bill') }}</h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="grid gap-8 md:grid-cols-2">
            <!-- Input Section -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body space-y-6">
                    <h2 class="card-title text-primary border-b pb-2">Anggota & Pesanan</h2>

                    <!-- Member Input -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Nama</span>
                        </label>
                        <input type="text" id="memberName" placeholder="Masukkan nama"
                            class="input input-bordered input-primary w-full" />
                    </div>

                    <!-- Order Details -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Detail Pesanan</span>
                        </label>
                        <div class="join join-vertical gap-3">
                            <input type="text" id="orderName" placeholder="Nama menu"
                                class="input input-bordered input-secondary" />
                            <input type="number" id="orderPrice" placeholder="Harga (Rp)"
                                class="input input-bordered input-secondary" />
                            <button onclick="addMemberWithOrder()"
                                class="btn btn-primary btn-block">
                                <i class="fas fa-plus mr-2"></i> Tambah Pesanan
                            </button>
                        </div>
                    </div>

                    <!-- Member List -->
                    <div class="divider"></div>
                    <div id="memberOrderList" class="space-y-4 overflow-y-auto max-h-[500px]"></div>
                </div>
            </div>

            <!-- Additional Costs & Discount -->
            <div class="space-y-6">
                <!-- Additional Costs Card -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-secondary border-b pb-2">Biaya Tambahan</h2>
                        <div class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Biaya Parkir</span>
                                </label>
                                <input type="number" id="parkingFee"
                                    class="input input-bordered input-secondary"
                                    placeholder="0" onchange="updateDefaultFees()">
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Biaya Layanan</span>
                                </label>
                                <input type="number" id="serviceFee"
                                    class="input input-bordered input-secondary"
                                    placeholder="0" onchange="updateDefaultFees()">
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Biaya Ongkos Kirim</span>
                                </label>
                                <input type="number" id="packagingFee"
                                    class="input input-bordered input-secondary"
                                    placeholder="0" onchange="updateDefaultFees()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discount Card -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-accent border-b pb-2">Diskon</h2>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Tipe Diskon</span>
                            </label>
                            <select id="discountType"
                                class="select select-bordered select-accent w-full"
                                onchange="toggleDiscountInputs()">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>

                        <div id="percentageInputs" class="space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Persentase (0-100%)</span>
                                </label>
                                <input type="number" id="discountPercent"
                                    class="input input-bordered input-accent"
                                    min="0" max="100" value="0"
                                    onchange="updateDiscount()">
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Maksimal Diskon (Rp)</span>
                                </label>
                                <input type="number" id="maxDiscount"
                                    class="input input-bordered input-accent"
                                    value="50000" onchange="updateMaxDiscount()">
                            </div>
                        </div>

                        <div id="fixedInputs" class="hidden space-y-4 mt-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Jumlah Diskon (Rp)</span>
                                </label>
                                <input type="number" id="fixedDiscount"
                                    class="input input-bordered input-accent"
                                    value="0" onchange="updateFixedDiscount()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculate Button -->
        <div class="text-center my-8">
            <button onclick="calculateSplit()"
                class="btn btn-primary btn-lg btn-wide gap-2">
                <i class="fas fa-calculator"></i>
                Hitung Pembagian
            </button>
        </div>

        <!-- Results Section -->
        <div id="result" class="hidden space-y-8 mb-8">
            <div id="resultDetails"></div>
        </div>
    </div>


        <script>
            let memberOrders = {};
            let defaultFees = {
                parking: 0,
                service: 0,
                packaging: 0
            };
            let discountType = 'percentage';
            let discountPercent = 0;
            let maxDiscount = 50000;
            let fixedDiscountAmount = 0;

            function addMemberWithOrder() {
                const memberName = document.getElementById('memberName').value.trim();
                const menuName = document.getElementById('orderName').value.trim();
                const price = parseFloat(document.getElementById('orderPrice').value);

                if (memberName && menuName && price > 0) {
                    if (!memberOrders[memberName]) {
                        memberOrders[memberName] = [];
                    }

                    memberOrders[memberName].push({
                        menu: menuName,
                        price: price,
                        total: price
                    });
                    updateMemberOrderList();

                    document.getElementById('orderName').value = '';
                    document.getElementById('orderPrice').value = '';
                }
            }

            function updateMemberOrderList() {
                const list = document.getElementById('memberOrderList');
                if (!list) return;
                list.innerHTML = Object.entries(memberOrders).map(([member, orders]) => {
                    const memberTotal = orders.reduce((sum, order) => sum + order.total, 0);

                    return `
                        <div class="card bg-base-200">
                            <div class="card-body p-4">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold text-lg">${member}</h3>
                                    <button onclick="removeMember('${member}')"
                                        class="btn btn-square btn-sm btn-ghost">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="divider my-0"></div>
                                <div class="space-y-2">
                                    ${orders.map((order, idx) => `
                                        <div class="flex justify-between items-center bg-base-100 p-2 rounded-lg">
                                            <div class="flex-1">
                                                <p class="font-medium">${order.menu}</p>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <p class="font-semibold">Rp ${order.price.toLocaleString()}</p>
                                                <button onclick="removeOrder('${member}', ${idx})"
                                                    class="btn btn-ghost btn-xs">Hapus</button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                <div class="divider my-0"></div>
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold">Total</span>
                                    <span class="font-bold">Rp ${memberTotal.toLocaleString()}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            function toggleDiscountInputs() {
                const type = document.getElementById('discountType').value;
                const percentageInputs = document.getElementById('percentageInputs');
                const fixedInputs = document.getElementById('fixedInputs');

                discountType = type;

                if (type === 'percentage') {
                    percentageInputs.classList.remove('hidden');
                    fixedInputs.classList.add('hidden');
                } else {
                    percentageInputs.classList.add('hidden');
                    fixedInputs.classList.remove('hidden');
                }
            }

            function updateDefaultFees() {
                defaultFees.parking = parseFloat(document.getElementById('parkingFee').value) || 0;
                defaultFees.service = parseFloat(document.getElementById('serviceFee').value) || 0;
                defaultFees.packaging = parseFloat(document.getElementById('packagingFee').value) || 0;
            }

            function updateDiscount() {
                discountPercent = parseFloat(document.getElementById('discountPercent').value) || 0;
                if (discountPercent > 100) {
                    discountPercent = 100;
                    document.getElementById('discountPercent').value = 100;
                }
            }

            function updateMaxDiscount() {
                maxDiscount = parseFloat(document.getElementById('maxDiscount').value) || 0;
            }

            function updateFixedDiscount() {
                fixedDiscountAmount = parseFloat(document.getElementById('fixedDiscount').value) || 0;
            }

            function removeMember(member) {
                delete memberOrders[member];
                updateMemberOrderList();
            }

            function removeOrder(member, orderIndex) {
                memberOrders[member].splice(orderIndex, 1);
                if (memberOrders[member].length === 0) {
                    delete memberOrders[member];
                }
                updateMemberOrderList();
            }

            function calculateSplit() {
                const members = Object.keys(memberOrders);
                if (members.length === 0) return;

                updateDefaultFees();
                updateDiscount();
                updateMaxDiscount();
                updateFixedDiscount();

                const totalOrders = Object.values(memberOrders).reduce((sum, orders) =>
                    sum + orders.reduce((memberSum, order) => memberSum + order.total, 0), 0);

                const totalFees = Object.values(defaultFees).reduce((sum, fee) => sum + fee, 0);

                // Calculate discount based on selected type
                let actualDiscount;
                if (discountType === 'percentage') {
                    const calculatedDiscount = (totalOrders * discountPercent / 100);
                    actualDiscount = Math.min(calculatedDiscount, maxDiscount);
                } else {
                    actualDiscount = Math.min(fixedDiscountAmount, totalOrders);
                }

                const discountRatio = actualDiscount / totalOrders;
                const feePerPerson = totalFees / members.length;

                const result = document.getElementById('result');
                const resultDetails = document.getElementById('resultDetails');

                result.classList.remove('hidden');
                let resultHTML = `
                    <div class="card bg-primary text-primary-content shadow-xl mb-6">
                        <div class="card-body">
                            <h2 class="card-title">Ringkasan Total</h2>
                            <div class="stats stats-vertical lg:stats-horizontal shadow">
                                <div class="stat">
                                    <div class="stat-title">Total Pesanan</div>
                                    <div class="stat-value">Rp ${totalOrders.toLocaleString()}</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title">Biaya Tambahan</div>
                                    <div class="stat-value">Rp ${totalFees.toLocaleString()}</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title">Total Diskon</div>
                                    <div class="stat-value">Rp ${actualDiscount.toLocaleString()}</div>
                                    <div class="stat-desc">${discountType === 'percentage' ? `(${discountPercent}%)` : '(Nominal)'}</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title">Grand Total</div>
                                    <div class="stat-value">Rp ${(totalOrders + totalFees - actualDiscount).toLocaleString()}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                resultHTML += '<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">';

                for (const member of members) {
                    const orders = memberOrders[member];
                    const orderTotal = orders.reduce((sum, order) => sum + order.total, 0);
                    const memberDiscount = orderTotal * discountRatio;
                    const total = orderTotal + feePerPerson - memberDiscount;

                    resultHTML += `
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title">${member}</h3>

                                <!-- Detail Pesanan -->
<div tabindex="0" class="collapse collapse-arrow bg-base-200">
    <div class="collapse-title font-medium">
        Detail Pesanan
    </div>
    <div class="collapse-content">
        ${orders.map(order => `
            <div class="text-sm mb-2">
                <div class="flex justify-between">
                    <span>${order.menu}</span>
                    <span>Rp ${order.price.toLocaleString()}</span>
                </div>
            </div>
        `).join('')}
        <div class="divider my-2"></div>
        <div class="flex justify-between font-semibold">
            <span>Subtotal Pesanan</span>
            <span>Rp ${orderTotal.toLocaleString()}</span>
        </div>
    </div>
</div>

                                <!-- Ringkasan Biaya -->
                                <div class="stats stats-vertical shadow mt-4">
                                    <div class="stat">
                                        <div class="stat-title">Subtotal Pesanan</div>
                                        <div class="stat-value text-lg">Rp ${orderTotal.toLocaleString()}</div>
                                    </div>
                                    ${totalFees > 0 ? `
                                    <div class="stat">
                                        <div class="stat-title">Biaya Tambahan</div>
                                        <div class="stat-value text-lg">Rp ${feePerPerson.toLocaleString()}</div>
                                        <div class="stat-desc">Dibagi ${members.length} orang</div>
                                    </div>
                                    ` : ''}
                                    ${actualDiscount > 0 ? `
                                    <div class="stat">
                                        <div class="stat-title">Diskon</div>
                                        <div class="stat-value text-lg">Rp ${memberDiscount.toLocaleString()}</div>
                                        <div class="stat-desc">Berdasarkan total pesanan</div>
                                    </div>
                                    ` : ''}
                                    <div class="stat bg-primary text-primary-content">
                                        <div class="stat-title text-secondary-content ">Total Bayar</div>
                                        <div class="stat-value">Rp ${Math.ceil(total).toLocaleString()}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }


                resultDetails.innerHTML = resultHTML;
            }
        </script>


</x-app-layout>
