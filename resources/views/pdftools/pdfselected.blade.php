<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-primary font-bold text-2xl">{{ __('Selected PDF') }}</h2>
        </div>
    </x-slot>

    <style>
        @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class=" bg-base-100 rounded-xl shadow-xl mx-4 px-4 ">
            <div class="card">
                <div class="mb-8">
                    <div id="dropZone" class="card bg-base-100 shadow-xl p-8 border-2 border-dashed border-primary/50 hover:border-primary transition-all duration-300">
                        <input type="file" id="fileInput" multiple accept=".pdf" class="hidden">
                        <label for="fileInput" class="cursor-pointer block text-center">
                            <div class="floating">
                                <svg class="mx-auto h-16 w-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Jatuhkan file pdf disini</h3>
                            <p class="mt-2 text-sm text-base-content/70">atau klik disini untuk memilih berkas</p>
                        </label>
                    </div>
                </div>

                <div id="loading" class="hidden mt-6 text-center">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div id="pageContainer" class="pb-4 hidden mt-10">
                    <div class="px-5 flex flex-wrap items-center justify-start gap-3 mb-6">
                        <button id="invertSelection" class="btn btn-secondary btn-sm hover:btn-secondary-focus transition-all duration-300 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Pilihan pembalik
                        </button>
                        <button id="clearPages" class="btn btn-error btn-sm hover:btn-error-focus transition-all duration-300 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Halaman
                        </button>
                        <button id="splitButton" class="btn btn-primary btn-sm hover:btn-primary-focus transition-all duration-300 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Unduh Pdf
                        </button>
                    </div>

                    <div id="pageGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8 gap-6"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const loading = document.getElementById('loading');
        const pageContainer = document.getElementById('pageContainer');
        const pageGrid = document.getElementById('pageGrid');
        const splitButton = document.getElementById('splitButton');
        const invertSelection = document.getElementById('invertSelection');
        const clearPages = document.getElementById('clearPages');

        let currentFile = null;
        let selectedPages = new Set();
        let pageOrder = [];

        // Initialize Sortable
        new Sortable(pageGrid, {
            animation: 150,
            ghostClass: 'bg-base-500',
            onEnd: function(evt) {
                const items = Array.from(pageGrid.children);
                pageOrder = items.map(item => parseInt(item.dataset.pageNum));
            }
        });

        // Event Listeners
        [['dragover', handleDragOver], ['dragleave', handleDragLeave], ['drop', handleDrop]]
            .forEach(([event, handler]) => dropZone.addEventListener(event, handler));

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) handleFile(file);
        });

        clearPages.addEventListener('click', () => {
            currentFile = null;
            selectedPages.clear();
            pageOrder = [];
            pageGrid.innerHTML = '';
            pageContainer.classList.add('hidden');
            fileInput.value = '';
        });

        invertSelection.addEventListener('click', () => {
            const allPages = Array.from(pageGrid.children);
            allPages.forEach(pageDiv => {
                const pageNum = parseInt(pageDiv.dataset.pageNum);
                togglePage(pageNum, pageDiv);
            });
        });

        function handleDragOver(e) {
            e.preventDefault();
            dropZone.classList.add('border-primary/70', 'bg-base-200');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            dropZone.classList.remove('border-primary/70', 'bg-base-200');
        }

        function handleDrop(e) {
            e.preventDefault();
            dropZone.classList.remove('border-primary/70', 'bg-base-200');
            const file = e.dataTransfer.files[0];
            if (file?.type === 'application/pdf') handleFile(file);
        }

        async function handleFile(file) {
            currentFile = file;
            selectedPages.clear();
            pageOrder = [];
            loading.classList.remove('hidden');
            pageContainer.classList.add('hidden');
            pageGrid.innerHTML = '';

            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
                
                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const viewport = page.getViewport({ scale: 0.5 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    await page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise;

                    const pageDiv = document.createElement('div');
                    pageDiv.dataset.pageNum = i;
                    pageDiv.className = 'relative group cursor-move';
                    pageDiv.innerHTML = `
                        <div class="relative aspect-[0.707] rounded-box overflow-hidden border-2 border-primary transition-all">
                            <img src="${canvas.toDataURL()}" class="w-full h-full object-contain" alt="Page ${i}">
                            <div class="absolute inset-0 bg-base-300 opacity-0 group-hover:opacity-50 transition-opacity"></div>
                            <div class="absolute bottom-0 left-0 right-0 bg-base-300 bg-opacity-75 p-2 text-center">
                                Halaman ${i}
                            </div>
                        </div>
                    `;

                    selectedPages.add(i);
                    pageOrder.push(i);
                    pageDiv.addEventListener('click', (e) => {
                        if (!e.target.closest('.handle')) {
                            togglePage(i, pageDiv);
                        }
                    });
                    pageGrid.appendChild(pageDiv);
                }

                pageContainer.classList.remove('hidden');
            } catch (error) {
                console.error('Error loading PDF:', error);
            } finally {
                loading.classList.add('hidden');
            }
        }

        function togglePage(pageNum, pageDiv) {
            if (selectedPages.has(pageNum)) {
                selectedPages.delete(pageNum);
                pageDiv.querySelector('.border-2').classList.remove('border-primary');
                pageDiv.querySelector('.border-2').classList.add('border-base-300');
                pageDiv.classList.add('opacity-50');
            } else {
                selectedPages.add(pageNum);
                pageDiv.querySelector('.border-2').classList.add('border-primary');
                pageDiv.querySelector('.border-2').classList.remove('border-base-300');
                pageDiv.classList.remove('opacity-50');
            }
        }

        splitButton.addEventListener('click', async () => {
            if (!currentFile || selectedPages.size === 0) return;

            splitButton.disabled = true;
            splitButton.innerHTML = '<span class="loading loading-spinner"></span> Processing...';

            try {
                const arrayBuffer = await currentFile.arrayBuffer();
                const pdfDoc = await PDFLib.PDFDocument.create();
                const originalPdf = await PDFLib.PDFDocument.load(arrayBuffer);

                const orderedSelectedPages = pageOrder.filter(pageNum => selectedPages.has(pageNum));
                const pageIndexes = orderedSelectedPages.map(num => num - 1);
                const pages = await pdfDoc.copyPages(originalPdf, pageIndexes);
                pages.forEach(page => pdfDoc.addPage(page));

                const newPdfBytes = await pdfDoc.save();
                const blob = new Blob([newPdfBytes], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'splitjoin.pdf';
                link.click();
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error splitting PDF:', error);
            } finally {
                splitButton.disabled = false;
                splitButton.textContent = 'Split Selected Pages';
            }
        });
    </script>
</x-app-layout>