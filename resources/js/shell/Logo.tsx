type MarkProps = { size?: number; variant?: 'light' | 'dark' };

/** Four modules bound by one thread — the plain-weave mark, not a lettermark. */
export function LogoMark({ size = 28, variant = 'light' }: MarkProps) {
    const module = variant === 'dark' ? 'var(--brand-indigo-soft)' : 'var(--brand-indigo)';
    const thread = variant === 'dark' ? 'var(--brand-brass-soft)' : 'var(--brand-brass)';

    return (
        <svg width={size} height={size} viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="4" width="22" height="22" rx="6" fill={module} />
            <rect x="38" y="4" width="22" height="22" rx="6" fill={module} />
            <rect x="4" y="38" width="22" height="22" rx="6" fill={module} />
            <rect x="38" y="38" width="22" height="22" rx="6" fill={module} />
            <path d="M26 15 H38" stroke={thread} strokeWidth={9} strokeLinecap="round" />
            <path d="M15 26 V38" stroke={thread} strokeWidth={9} strokeLinecap="round" />
            <path d="M49 26 V38" stroke={thread} strokeWidth={9} strokeLinecap="round" />
            <path d="M26 49 H38" stroke={thread} strokeWidth={9} strokeLinecap="round" />
            <circle cx="32" cy="32" r="7.5" fill={thread} />
        </svg>
    );
}

export function LogoLockup({ size = 24, variant = 'dark', textColor = '#fff' }: MarkProps & { textColor?: string }) {
    return (
        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
            <LogoMark size={size} variant={variant} />
            <span style={{ fontFamily: 'var(--brand-font-display)', fontWeight: 600, fontSize: size * 0.8, color: textColor }}>
                Moduloom
            </span>
        </div>
    );
}
