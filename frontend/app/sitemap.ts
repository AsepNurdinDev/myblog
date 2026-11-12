import type { MetadataRoute } from 'next';
import { getPosts, getProjects, getCategories, getTags } from '@/lib/api/resources';
export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const base = 'https://asepblog.online';
  const fixed = ['', '/articles', '/projects', '/about', '/categories', '/tags'].map((path) => ({
    url: `${base}${path}`,
    lastModified: new Date(),
  }));
  try {
    const [firstPosts, firstProjects, categories, tags] = await Promise.all([
      getPosts({ page: 1, per_page: 50 }),
      getProjects(1, 50),
      getCategories(),
      getTags(),
    ]);
    const postPages = await Promise.all(
      Array.from({ length: Math.max(0, firstPosts.meta.last_page - 1) }, (_, i) =>
        getPosts({ page: i + 2, per_page: 50 }),
      ),
    );
    const projectPages = await Promise.all(
      Array.from({ length: Math.max(0, firstProjects.meta.last_page - 1) }, (_, i) =>
        getProjects(i + 2, 50),
      ),
    );
    const posts = [...firstPosts.data, ...postPages.flatMap((page) => page.data)];
    const projects = [...firstProjects.data, ...projectPages.flatMap((page) => page.data)];
    return [
      ...fixed,
      ...posts.map((post) => ({
        url: `${base}/articles/${post.slug}`,
        lastModified: post.published_at ? new Date(post.published_at) : new Date(),
      })),
      ...projects.map((project) => ({
        url: `${base}/projects/${project.slug}`,
        lastModified: project.completed_at ? new Date(project.completed_at) : new Date(),
      })),
      ...categories.map((category) => ({
        url: `${base}/categories/${category.slug}`,
        lastModified: new Date(),
      })),
      ...tags.map((tag) => ({ url: `${base}/tags/${tag.slug}`, lastModified: new Date() })),
    ];
  } catch {
    return fixed;
  }
}
