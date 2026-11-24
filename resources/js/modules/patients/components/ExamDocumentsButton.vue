<template>
    <button class="documents-trigger" type="button" @click="openDialog">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg>
        Documentos <span>{{ documents.length }}</span>
    </button>

    <Teleport to="body">
        <div v-if="dialogOpen" class="document-backdrop" @click.self="closeDialog">
            <section class="document-dialog" role="dialog" aria-modal="true" :aria-labelledby="dialogTitleId">
                <header class="dialog-header">
                    <div>
                        <span class="dialog-eyebrow">ANEXOS DO EXAME</span>
                        <h2 :id="dialogTitleId">{{ examName }}</h2>
                    </div>
                    <button class="dialog-close" type="button" aria-label="Fechar documentos" @click="closeDialog">×</button>
                </header>

                <p class="demo-disclaimer">Arquivos de demonstração são fictícios e não têm valor clínico ou legal.</p>

                <div v-if="error" class="document-error" role="alert">{{ error }}</div>

                <div v-if="selectedDocument && previewUrl" class="document-preview">
                    <div class="preview-toolbar">
                        <strong>{{ selectedDocument.title }}</strong>
                        <button type="button" @click="closePreview">Voltar aos documentos</button>
                    </div>
                    <iframe :src="previewUrl" :title="selectedDocument.original_file_name"></iframe>
                </div>

                <div v-else-if="documents.length" class="document-list">
                    <article v-for="document in documents" :key="document.id" class="document-row">
                        <span class="document-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                        <div class="document-copy">
                            <strong>{{ document.title }}</strong>
                            <span>{{ document.original_file_name }} · {{ formatFileSize(document.file_size) }}</span>
                            <small>
                                {{ document.uploaded_by ? `Anexado por ${document.uploaded_by}` : 'Anexado pelo médico' }}
                                <template v-if="document.uploaded_at"> · {{ formatDate(document.uploaded_at) }}</template>
                            </small>
                        </div>
                        <button
                            class="document-open"
                            type="button"
                            :disabled="loadingDocumentId === document.id"
                            @click="previewDocument(document)"
                        >
                            {{ loadingDocumentId === document.id ? 'Carregando...' : 'Visualizar' }}
                        </button>
                    </article>
                </div>
                <div v-else class="document-empty">
                    <strong>Nenhum documento anexado</strong>
                    <span>Laudos, atestados e outros arquivos deste exame aparecerão aqui.</span>
                </div>
            </section>
        </div>
    </Teleport>
</template>

<script setup>
import axios from 'axios';
import { onBeforeUnmount, ref, useId } from 'vue';
import { formatDate } from '../../../shared/utils/date';
import { formatFileSize } from '../../../shared/utils/fileSize';
import { ExamDocumentService } from '../services/ExamDocumentService';

const props = defineProps({
    patientId: { type: [Number, String], required: true },
    examId: { type: [Number, String], required: true },
    examName: { type: String, required: true },
    documents: { type: Array, default: () => [] },
});

const dialogTitleId = useId();
const dialogOpen = ref(false);
const loadingDocumentId = ref(null);
const selectedDocument = ref(null);
const previewUrl = ref('');
const error = ref('');

function openDialog() {
    dialogOpen.value = true;
    error.value = '';
}

function closePreview() {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = '';
    selectedDocument.value = null;
}

function closeDialog() {
    closePreview();
    dialogOpen.value = false;
    error.value = '';
}

async function previewDocument(document) {
    closePreview();
    error.value = '';
    loadingDocumentId.value = document.id;

    try {
        const content = await ExamDocumentService.getContent(props.patientId, props.examId, document.id);
        previewUrl.value = URL.createObjectURL(new Blob([content], { type: document.mime_type }));
        selectedDocument.value = document;
    } catch (requestError) {
        if (!axios.isAxiosError(requestError)) {
            throw requestError;
        }

        error.value = 'Não foi possível abrir o documento. Tente novamente.';
    } finally {
        loadingDocumentId.value = null;
    }
}

onBeforeUnmount(closePreview);
</script>

<style scoped>
.documents-trigger { display: inline-flex; align-items: center; gap: 6px; padding: 6px 9px; border: 1px solid #dfe6f2; border-radius: 7px; color: #4262b4; background: #fff; font-size: 9px; font-weight: 700; white-space: nowrap; }
.documents-trigger:hover { border-color: #b8c9f0; background: #f7f9ff; }
.documents-trigger svg { width: 14px; height: 14px; }
.documents-trigger span { display: grid; min-width: 17px; height: 17px; place-items: center; border-radius: 6px; color: #fff; background: #4262b4; font-size: 8px; }
.document-backdrop { position: fixed; inset: 0; z-index: 100; display: grid; place-items: center; padding: 20px; background: rgb(17 29 52 / 54%); }
.document-dialog { width: min(760px, 100%); max-height: min(800px, 90vh); display: flex; flex-direction: column; overflow: hidden; border: 1px solid #e8edf5; border-radius: 14px; background: #fff; box-shadow: 0 24px 80px rgb(11 28 62 / 24%); }
.dialog-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; padding: 20px 22px 15px; border-bottom: 1px solid #edf0f5; }
.dialog-eyebrow { color: #5974b6; font-size: 9px; font-weight: 700; letter-spacing: 1px; }
.dialog-header h2 { margin: 6px 0 0; color: #293a57; font-size: 16px; }
.dialog-close { width: 32px; height: 32px; border: 0; border-radius: 8px; color: #64728a; background: #f3f5f9; font-size: 22px; line-height: 1; }
.demo-disclaimer { margin: 0; padding: 10px 22px; color: #77859a; background: #f8faff; font-size: 10px; }
.document-error { margin: 12px 20px 0; padding: 10px 12px; border-radius: 8px; color: #9c3d44; background: #fff1f1; font-size: 11px; }
.document-list { display: grid; gap: 9px; overflow-y: auto; padding: 16px 20px 20px; }
.document-row { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #edf0f5; border-radius: 10px; }
.document-icon { width: 36px; height: 36px; display: grid; flex: none; place-items: center; border-radius: 10px; color: #4262b4; background: #eef3ff; }
.document-icon svg { width: 18px; height: 18px; }
.document-copy { display: grid; flex: 1; gap: 4px; min-width: 0; }
.document-copy strong { color: #354560; font-size: 11px; }
.document-copy span, .document-copy small { overflow-wrap: anywhere; color: #7e8ba0; font-size: 9px; }
.document-open, .preview-toolbar button { padding: 7px 10px; border: 1px solid #dfe6f2; border-radius: 7px; color: #4262b4; background: #fff; font-size: 9px; font-weight: 700; white-space: nowrap; }
.document-open:disabled { cursor: wait; opacity: .6; }
.document-empty { min-height: 170px; display: grid; align-content: center; justify-items: center; gap: 8px; padding: 20px; color: #8995a8; font-size: 11px; text-align: center; }
.document-empty strong { color: #354560; font-size: 13px; }
.document-preview { min-height: 360px; display: flex; flex-direction: column; flex: 1; padding: 12px 18px 18px; }
.preview-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding-bottom: 10px; color: #354560; font-size: 11px; }
.document-preview iframe { width: 100%; min-height: 360px; flex: 1; border: 1px solid #e8edf5; border-radius: 8px; background: #f8faff; }

@media (max-width: 520px) {
    .document-backdrop { padding: 10px; }
    .document-row { align-items: flex-start; flex-wrap: wrap; }
    .document-copy { flex-basis: calc(100% - 50px); }
    .document-open { margin-left: 48px; }
    .dialog-header { padding-right: 16px; padding-left: 16px; }
}
</style>
