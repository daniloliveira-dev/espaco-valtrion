const validateEmail = (value) => {
    if (!value?.trim()) return 'Informe seu e-mail.';
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim()) || 'Informe um e-mail válido.';
};

const validatePassword = (value) => {
    if (!value) return 'Informe sua senha.';
    return value.length >= 8 || 'A senha deve ter pelo menos 8 caracteres.';
};

export const loginSchema = {
    email: validateEmail,
    password: (value) => value?.length > 0 || 'Informe sua senha.',
};

export const registerSchema = {
    name: (value) => {
        const name = value?.trim() ?? '';
        if (name.length < 2) return 'Informe seu nome completo.';
        return name.length <= 120 || 'Use até 120 caracteres.';
    },
    email: validateEmail,
    password: validatePassword,
    password_confirmation: (value, context) => {
        if (!value) return 'Confirme sua senha.';
        return value === context.form.password || 'As senhas não conferem.';
    },
};
