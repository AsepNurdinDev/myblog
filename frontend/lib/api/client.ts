import type { ApiErrorBody } from '@/types/api';

const DEFAULT_BASE_URL = 'https://api.asepblog.online/api/v1';
const API_BASE_URL = (process.env.NEXT_PUBLIC_API_BASE_URL || DEFAULT_BASE_URL).replace(/\/$/, '');

export class ApiError extends Error {
  status: number;
  errors?: Record<string, string[]>;
  retryAfter?: number;
  constructor(message: string, status: number, body?: ApiErrorBody) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.errors = body?.errors;
    this.retryAfter = body?.retry_after;
  }
}

export async function apiFetch<T>(path: string, init: RequestInit = {}): Promise<T> {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 10_000);
  try {
    const response = await fetch(`${API_BASE_URL}${path.startsWith('/') ? path : `/${path}`}`, {
      ...init,
      headers: {
        Accept: 'application/json',
        ...(init.body ? { 'Content-Type': 'application/json' } : {}),
        ...init.headers,
      },
      cache: 'no-store',
      signal: init.signal ?? controller.signal,
    });
    const body: unknown = await response.json().catch(() => null);
    if (!response.ok) {
      const errorBody = body && typeof body === 'object' ? (body as ApiErrorBody) : undefined;
      throw new ApiError(
        errorBody?.message || `API request failed (${response.status})`,
        response.status,
        errorBody,
      );
    }
    return body as T;
  } catch (error) {
    if (error instanceof ApiError) throw error;
    if (error instanceof Error && error.name === 'AbortError')
      throw new ApiError('Permintaan ke server melewati batas waktu. Coba lagi.', 408);
    throw new ApiError('Tidak dapat terhubung ke server. Periksa koneksi dan coba lagi.', 0);
  } finally {
    clearTimeout(timeout);
  }
}
