<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-primary font-bold text-2xl">{{ __('PDF Merge') }}</h2>
        </div>
    </x-slot>

    <style>
        .drag-over {
            border-color: #4f46e5 !important;
            background-color: #eef2ff !important;
        }
        .thumbnail-container {
            width: 180px;
            height: 254px;
            perspective: 1000px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: move;
            position: relative;
            z-index: 1;
        }
        .thumbnail-container.dragging {
            opacity: 0.9;
            transform: scale(1.05) rotate(2deg);
            z-index: 10;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        .thumbnail-container.drag-over {
            transform: scale(0.95);
        }
        .thumbnail-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            will-change: transform;
        }
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        .drop-indicator {
            position: absolute;
            inset: 0;
            background: rgba(79, 70, 229, 0.1);
            border: 2px dashed #4f46e5;
            border-radius: 0.75rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .drag-over .drop-indicator {
            opacity: 1;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Upload Area -->
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

        <!-- Files Container -->
        <div class="card bg-base-100 shadow-xl p-6 mt-5">
            <div id="fileList" class="file-grid"></div>
            
            <!-- Merge Button -->
            <div id="mergeButtonContainer" class="hidden mt-8 px-4">
                <button id="mergeButton" class="btn btn-primary btn-block gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Satukan Pdf
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let files = [];
        let draggedItem = null;
        let draggedIndex = null;
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const mergeButton = document.getElementById('mergeButton');
        const mergeButtonContainer = document.getElementById('mergeButtonContainer');

        async function generateThumbnail(file) {
            try {
                const arrayBuffer = await file.arrayBuffer();
                const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
                const page = await pdf.getPage(1);
                const viewport = page.getViewport({ scale: 0.5 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                
                return canvas.toDataURL();
            } catch (error) {
                console.error('Error generating thumbnail:', error);
                return null;
            }
        }

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const droppedFiles = Array.from(e.dataTransfer.files).filter(file => file.type === 'application/pdf');
            handleFiles(droppedFiles);
        });

        fileInput.addEventListener('change', (e) => {
            const selectedFiles = Array.from(e.target.files).filter(file => file.type === 'application/pdf');
            handleFiles(selectedFiles);
        });

        async function handleFiles(newFiles) {
            for (const file of newFiles) {
                const thumbnail = await generateThumbnail(file);
                files.push({ file, thumbnail });
            }
            updateFileList();
            updateMergeButton();
        }

        function removeFile(index) {
            files.splice(index, 1);
            updateFileList();
            updateMergeButton();
        }

        function handleDragStart(e) {
            draggedItem = this;
            draggedIndex = parseInt(this.dataset.index);
            requestAnimationFrame(() => {
                this.classList.add('dragging');
                document.querySelectorAll('.thumbnail-container').forEach(item => {
                    if (item !== draggedItem) {
                        item.style.transition = 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    }
                });
            });
        }

        function handleDragEnd(e) {
            requestAnimationFrame(() => {
                this.classList.remove('dragging');
                document.querySelectorAll('.thumbnail-container').forEach(item => {
                    item.classList.remove('drag-over');
                    item.style.transition = '';
                });
            });
            draggedItem = null;
            draggedIndex = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }

        function handleDragEnter(e) {
            e.preventDefault();
            if (this === draggedItem) return;
            this.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            if (this === draggedItem) return;
            this.classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            if (!draggedItem || draggedItem === this) return;

            const toIndex = parseInt(this.dataset.index);
            
            requestAnimationFrame(() => {
                const [movedItem] = files.splice(draggedIndex, 1);
                files.splice(toIndex, 0, movedItem);
                updateFileList();
            });
        }

        function updateFileList() {
            fileList.innerHTML = '';
            files.forEach(({ file, thumbnail }, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileElement = document.createElement('div');
                fileElement.className = 'thumbnail-container';
                fileElement.draggable = true;
                fileElement.dataset.index = index;
                
                fileElement.innerHTML = `
                    <div class="thumbnail-inner bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="relative h-full">
                            <div class="absolute inset-0 ${!thumbnail ? 'bg-gray-100' : ''}">
                                ${thumbnail ? 
                                    `<img src="${thumbnail}" alt="PDF thumbnail" class="w-full h-full object-cover">` :
                                    `<div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>`
                                }
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                                <p class="text-white text-sm font-medium truncate">${file.name}</p>
                                <p class="text-gray-300 text-xs">${fileSize} MB</p>
                            </div>
                            <button onclick="removeFile(${index})" class="absolute top-2 right-2 p-1 bg-white/90 hover:bg-red-100 rounded-full shadow-lg transition-colors duration-200">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="drop-indicator"></div>
                        </div>
                    </div>
                `;

                fileElement.addEventListener('dragstart', handleDragStart);
                fileElement.addEventListener('dragend', handleDragEnd);
                fileElement.addEventListener('dragover', handleDragOver);
                fileElement.addEventListener('dragenter', handleDragEnter);
                fileElement.addEventListener('dragleave', handleDragLeave);
                fileElement.addEventListener('drop', handleDrop);

                fileList.appendChild(fileElement);
            });
        }

        function updateMergeButton() {
            mergeButtonContainer.className = files.length > 1 ? 'mt-8 px-4' : 'mt-8 px-4 hidden';
        }

        mergeButton.addEventListener('click', async () => {
            if (files.length < 2) return;

            mergeButton.disabled = true;
            mergeButton.innerHTML = `
                <span class="flex items-center justify-center">
                    <svg class="animate-spin h-6 w-6 mr-3" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Merging PDFs...
                </span>
            `;

            try {
                const mergedPdf = await PDFLib.PDFDocument.create();
                
                for (const { file } of files) {
                    const fileBuffer = await file.arrayBuffer();
                    const pdf = await PDFLib.PDFDocument.load(fileBuffer);
                    const pages = await mergedPdf.copyPages(pdf, pdf.getPageIndices());
                    pages.forEach(page => mergedPdf.addPage(page));
                }

                const mergedPdfFile = await mergedPdf.save();
                
                const blob = new Blob([mergedPdfFile], { type: 'application/pdf' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'merged.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);

                files = [];
                updateFileList();
                updateMergeButton();
            } catch (error) {
                console.error('Error merging PDFs:', error);
                alert('Failed to merge PDFs. Please try again.');
            } finally {
                mergeButton.disabled = false;
                mergeButton.innerHTML = `
                    <span class="flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Merge PDFs
                    </span>
                `;
            }
        });
    </script>
</x-app-layout>

