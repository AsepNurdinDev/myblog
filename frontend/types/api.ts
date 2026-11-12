export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  posts_count?: number;
}
export interface Tag {
  id: number;
  name: string;
  slug: string;
  posts_count?: number;
}
export interface Skill {
  id: number;
  name: string;
  slug: string;
}
export interface Comment {
  id: number;
  name: string;
  content: string;
  created_at: string | null;
}
export interface Profile {
  name: string | null;
  headline: string | null;
  bio: string | null;
  bio_text: string | null;
  location: string | null;
  avatar: string | null;
  website: string | null;
  github: string | null;
  linkedin: string | null;
  instagram: string | null;
  twitter: string | null;
  skills: Skill[];
}
export interface Post {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  cover_image: string | null;
  published_at: string | null;
  category: Category | null;
  tags: Tag[];
  reading_time?: number;
}
export type ContentBlock =
  | { type: 'text'; data: { content: string | null } }
  | { type: 'image'; data: { url: string | null; caption: string | null } }
  | {
      type: 'code';
      data: { language?: string | null; filename?: string | null; code?: string | null };
    }
  | { type: 'quote'; data: { quote?: string | null; author?: string | null } }
  | { type: string; data: Record<string, unknown> };
export interface PostDetail extends Post {
  content: ContentBlock[];
  reading_time: number;
  seo: { title: string; description: string | null };
  comments: Comment[];
  comments_count: number;
}
export interface Project {
  id: number;
  title: string;
  slug: string;
  description: string | null;
  thumbnail: string | null;
  url: string | null;
  github_url: string | null;
  status: 'ongoing' | 'completed' | 'archived' | null;
  started_at: string | null;
  completed_at: string | null;
  skills: Skill[];
  content?: string | null;
}
export interface PaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  path: string;
  per_page: number;
  to: number | null;
  total: number;
}
export interface Paginated<T> {
  data: T[];
  links: { first: string | null; last: string | null; prev: string | null; next: string | null };
  meta: PaginationMeta;
}
export interface HomeData {
  profile: Profile | null;
  latest_posts: Post[];
  latest_projects: Project[];
  categories: Category[];
}
export interface ApiErrorBody {
  message?: string;
  errors?: Record<string, string[]>;
  retry_after?: number;
}
