import { apiFetch } from './client';
import type {
  Category,
  Comment,
  HomeData,
  Paginated,
  Post,
  PostDetail,
  Profile,
  Project,
  Tag,
} from '@/types/api';

export async function getHome() {
  return (await apiFetch<{ data: HomeData }>('/home')).data;
}
export async function getPosts(
  params: {
    page?: number;
    search?: string;
    category?: string;
    tag?: string;
    per_page?: number;
  } = {},
) {
  const query = new URLSearchParams();
  Object.entries(params).forEach(([k, v]) => {
    if (v !== undefined && v !== '') query.set(k, String(v));
  });
  return apiFetch<Paginated<Post>>(`/posts${query.size ? `?${query}` : ''}`);
}
export async function getPost(slug: string) {
  return (await apiFetch<{ data: PostDetail }>(`/posts/${encodeURIComponent(slug)}`)).data;
}
export async function getCategories() {
  return (await apiFetch<{ data: Category[] }>('/categories')).data;
}
export async function getCategory(slug: string, page = 1) {
  return apiFetch<Paginated<Post> & { category: Category }>(
    `/categories/${encodeURIComponent(slug)}?page=${page}`,
  );
}
export async function getTags() {
  return (await apiFetch<{ data: Tag[] }>('/tags')).data;
}
export async function getTag(slug: string, page = 1) {
  return apiFetch<Paginated<Post> & { tag: Tag }>(`/tags/${encodeURIComponent(slug)}?page=${page}`);
}
export async function getProjects(page = 1, perPage = 9) {
  return apiFetch<Paginated<Project>>(`/projects?page=${page}&per_page=${perPage}`);
}
export async function getProject(slug: string) {
  return (await apiFetch<{ data: Project }>(`/projects/${encodeURIComponent(slug)}`)).data;
}
export async function getProfile() {
  return (await apiFetch<{ data: Profile }>('/profile')).data;
}
export async function submitComment(
  slug: string,
  payload: { name: string; email: string; content: string; website: string },
) {
  return apiFetch<{ message: string; status: string; data: Comment | null }>(
    `/posts/${encodeURIComponent(slug)}/comments`,
    { method: 'POST', body: JSON.stringify(payload) },
  );
}
