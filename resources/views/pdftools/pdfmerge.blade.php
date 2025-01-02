<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-primary">{{ __('Merge PDF') }}</h2>
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

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="px-4 mx-4 rounded-xl shadow-xl bg-base-100">
            <div class="card">
                <div class="mb-8">
                    <div id="dropZone" class="p-8 border-2 border-dashed shadow-xl transition-all duration-300 card bg-base-100 border-primary/50 hover:border-primary">
                        <input type="file" id="fileInput" multiple accept=".pdf" class="hidden">
                        <label for="fileInput" class="block text-center cursor-pointer">
                            <div class="floating">
                                <svg class="mx-auto w-16 h-16 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Jatuhkan file PDF disini</h3>
                            <p class="mt-2 text-sm text-base-content/70">atau klik disini untuk memilih beberapa berkas</p>
                        </label>
                    </div>
                </div>

                <div id="loading" class="hidden mt-6 text-center">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <div id="fileContainer" class="hidden pb-4 mt-10">
                    <div class="flex flex-wrap gap-3 justify-start items-center px-5 mb-6">
                        <button id="clearFiles" class="shadow-lg transition-all duration-300 btn btn-error btn-sm hover:btn-error-focus">
                            <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Semua
                        </button>
                        <button id="sortByName" class="shadow-lg transition-all duration-300 btn btn-secondary btn-sm hover:btn-secondary-focus">
                            Urutkan berdasarkan nama
                        </button>
                        <button id="mergeButton" class="shadow-lg transition-all duration-300 btn btn-primary btn-sm hover:btn-primary-focus">
                            Gabung PDF
                        </button>
                    </div>

                    <div id="fileGrid" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"></div>
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
        const fileContainer = document.getElementById('fileContainer');
        const fileGrid = document.getElementById('fileGrid');
        const mergeButton = document.getElementById('mergeButton');
        const clearFiles = document.getElementById('clearFiles');
        const sortByName = document.getElementById('sortByName');

        let pdfFiles = [];
        let pdfThumbnails = new Map();

        // Initialize Sortable
        new Sortable(fileGrid, {
            animation: 150,
            ghostClass: 'bg-base-500',
            onEnd: function(evt) {
                const items = Array.from(fileGrid.children);
                pdfFiles = items.map(item => pdfFiles[parseInt(item.dataset.index)]);
            }
        });

        // Event Listeners
        [['dragover', handleDragOver], ['dragleave', handleDragLeave], ['drop', handleDrop]]
            .forEach(([event, handler]) => dropZone.addEventListener(event, handler));

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files).filter(file => file.type === 'application/pdf');
            if (files.length > 0) handleFiles(files);
        });

        clearFiles.addEventListener('click', () => {
            pdfFiles = [];
            pdfThumbnails.clear();
            fileGrid.innerHTML = '';
            fileContainer.classList.add('hidden');
            fileInput.value = '';
        });

        sortByName.addEventListener('click', () => {
            pdfFiles.sort((a, b) => a.name.localeCompare(b.name));
            refreshFileGrid();
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
            const files = Array.from(e.dataTransfer.files).filter(file => file.type === 'application/pdf');
            if (files.length > 0) handleFiles(files);
        }

        async function handleFiles(files) {
            loading.classList.remove('hidden');
            fileContainer.classList.add('hidden');

            try {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const arrayBuffer = await file.arrayBuffer();
                    const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
                    const firstPage = await pdf.getPage(1);
                    const viewport = firstPage.getViewport({ scale: 0.5 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    await firstPage.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise;

                    const thumbnail = canvas.toDataURL();
                    pdfThumbnails.set(file.name, {
                        thumbnail: thumbnail,
                        numPages: pdf.numPages
                    });

                    const fileDiv = document.createElement('div');
                    fileDiv.dataset.index = pdfFiles.length;
                    fileDiv.className = 'relative group cursor-move';
                    fileDiv.innerHTML = `
                        <div class="relative aspect-[0.707] rounded-box overflow-hidden border-2 border-primary transition-all">

                            <img src="${thumbnail}" class="object-contain w-full h-full" alt="PDF ${file.name}">
                            <div class="absolute inset-0 opacity-0 transition-opacity bg-base-300 group-hover:opacity-50"></div>
                            <div class="absolute right-0 bottom-0 left-0 p-2 text-center bg-opacity-75 bg-base-300">
                                ${file.name} (${pdf.numPages} halaman)
                            </div>
                            <button class="absolute top-2 right-2 btn btn-error btn-sm btn-circle" onclick="removeFile(${pdfFiles.length})">
                                ×
                            </button>
                        </div>
                    `;

                    pdfFiles.push(file);
                    fileGrid.appendChild(fileDiv);
                }

                fileContainer.classList.remove('hidden');
            } catch (error) {
                console.error('Error loading PDFs:', error);
            } finally {
                loading.classList.add('hidden');
            }
        }

        function refreshFileGrid() {
            fileGrid.innerHTML = '';
            pdfFiles.forEach((file, i) => {
                const fileInfo = pdfThumbnails.get(file.name);
                const fileDiv = document.createElement('div');
                fileDiv.dataset.index = i;
                fileDiv.className = 'relative group cursor-move';
                fileDiv.innerHTML = `
                    <div class="relative aspect-[0.707] rounded-box overflow-hidden border-2 border-primary transition-all">
                        <img src="${fileInfo.thumbnail}" class="object-contain w-full h-full" alt="PDF ${file.name}">
                        <div class="absolute inset-0 opacity-0 transition-opacity bg-base-300 group-hover:opacity-50"></div>
                        <div class="absolute right-0 bottom-0 left-0 p-2 text-center bg-opacity-75 bg-base-300">

                            ${file.name} (${fileInfo.numPages} halaman)
                        </div>
                        <button class="absolute top-2 right-2 btn btn-error btn-sm btn-circle" onclick="removeFile(${i})">
                            ×
                        </button>
                    </div>
                `;
                fileGrid.appendChild(fileDiv);
            });
        }

        function removeFile(index) {
            const fileName = pdfFiles[index].name;
            pdfFiles.splice(index, 1);
            pdfThumbnails.delete(fileName);
            refreshFileGrid();
            if (pdfFiles.length === 0) {
                fileContainer.classList.add('hidden');
            }
        }

        mergeButton.addEventListener('click', async () => {
            if (pdfFiles.length === 0) return;

            mergeButton.disabled = true;
            mergeButton.innerHTML = '<span class="loading loading-spinner"></span> Processing...';

            try {
                const mergedPdf = await PDFLib.PDFDocument.create();

                for (const file of pdfFiles) {
                    const arrayBuffer = await file.arrayBuffer();
                    const pdf = await PDFLib.PDFDocument.load(arrayBuffer);
                    const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                    pages.forEach(page => mergedPdf.addPage(page));
                }

                const mergedPdfBytes = await mergedPdf.save();
                const blob = new Blob([mergedPdfBytes], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'MergedDocument.pdf';
                link.click();
                URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error merging PDFs:', error);
            } finally {
                mergeButton.disabled = false;
                mergeButton.textContent = 'Gabung PDF';
            }
        });
    </script>
</x-app-layout>
