import api from '../../../shared/services/api';

export const ExamDocumentService = {
    async getContent(patientId, examId, documentId) {
        const { data } = await api.get(
            `/patients/${patientId}/exams/${examId}/documents/${documentId}/content`,
            { responseType: 'blob' },
        );

        return data;
    },

    async downloadContent(patientId, examId, documentId) {
        const { data } = await api.get(
            `/patients/${patientId}/exams/${examId}/documents/${documentId}/download`,
            { responseType: 'blob' },
        );

        return data;
    },
};
