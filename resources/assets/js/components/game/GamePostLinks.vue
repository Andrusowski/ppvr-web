<template>
    <div class="post-links">
        <p class="uk-text-muted">{{ title }}</p>
        <div class="uk-flex uk-flex-column uk-flex-middle">
            <a
                v-for="post in posts"
                :key="post.id"
                :href="'/post/' + post.id"
                class="post-link-button"
            >
                <span class="post-link-title">{{ post.title }}</span>
                <span class="post-link-meta">
                    <span class="post-link-score">{{ post.score }} points</span>
                    <span class="post-link-author">{{ authorPrefix }}u/{{ post.author }}</span>
                </span>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: 'GamePostLinks',
    props: {
        posts: {
            type: Array,
            required: true,
        },
        title: {
            type: String,
            default: 'Posts:',
        },
        authorPrefix: {
            type: String,
            default: 'by ',
        },
    },
};
</script>

<style scoped>
.post-links {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.post-link-button {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    background-color: #f8f8f8;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s, border-color 0.2s, transform 0.2s;
}

[data-theme="dark"] .post-link-button {
    background-color: #2a2a2a;
    border-color: #3d3d3d;
}

.post-link-button:hover {
    background-color: #fff;
    border-color: #1e87f0;
    transform: translateX(4px);
    text-decoration: none;
}

[data-theme="dark"] .post-link-button:hover {
    background-color: #3a3a3a;
    border-color: #5eb3ff;
}

.post-link-title {
    font-size: 0.9rem;
    color: #333;
    font-weight: 500;
    line-height: 1.3;
    word-wrap: break-word;
    text-align: left;
}

[data-theme="dark"] .post-link-title {
    color: #e0e0e0;
}

.post-link-meta {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.25rem;
    font-size: 0.75rem;
}

.post-link-score {
    color: #ff4500;
    font-weight: 500;
}

.post-link-author {
    color: #999;
}

[data-theme="dark"] .post-link-author {
    color: #a0a0a0;
}
</style>
