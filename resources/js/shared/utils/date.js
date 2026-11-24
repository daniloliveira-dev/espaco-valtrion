export function formatDate(value) {
    if (!value) return 'Não informado';

    const [year, month, day] = String(value).slice(0, 10).split('-').map(Number);
    const date = new Date(year, month - 1, day, 12);

    if (Number.isNaN(date.getTime())) return String(value);

    return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'medium' }).format(date);
}
