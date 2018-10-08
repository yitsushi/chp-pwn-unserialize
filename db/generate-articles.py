#!/usr/bin/env python

import random

with open('articles') as content:
    articles = list(filter(None, content.read().split('\n')))

with open('authors') as content:
    authors = list(filter(None, content.read().split('\n')))

def pick_article_title():
    random.shuffle(articles)
    return articles.pop()

def select_author():
    random.shuffle(authors)
    return authors[0]

print("<?php\n\n$_articles = [")
id = 0
while len(articles) > 0:
    print(f"""
    [
        'id'         => {id},
        'title'      => "{pick_article_title()}",
        'author'     => "{select_author()}",
        'comments'   => {random.randint(0, 200)},
        'page_views' => {random.randint(1_000, 1_000_000)},
    ],""")
    id += 1

print("];")
