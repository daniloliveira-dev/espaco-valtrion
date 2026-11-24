const statusLabels = {
    completed: 'Concluído',
    scheduled: 'Agendado',
    requested: 'Solicitado',
    cancelled: 'Cancelado',
};

const statusClasses = {
    completed: 'status-completed',
    scheduled: 'status-scheduled',
    requested: 'status-requested',
    cancelled: 'status-cancelled',
};

export function getExamStatusLabel(status) {
    return statusLabels[status] ?? 'Status não disponível';
}

export function getExamStatusClass(status) {
    return statusClasses[status] ?? 'status-unknown';
}
