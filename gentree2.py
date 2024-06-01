import os
import re

NEW_NAVS = [
    {
        'title': 'Alma Mater',
        'link': '#alma-mater',
        'children': [
            { 'title': 'History of the College', 'link': '#history-of-the-college' },
            { 'title': 'Our Spirituality', 'link': '#our-spirituality' },
            { 'title': 'Founders', 'link': '#founders' },
            { 'title': 'Motto, Vision & Mission', 'link': '#motto-vision-mission' },
            { 'title': 'Coat of Arms', 'link': '#coat-of-arms' },
            { 'title': 'College Anthem', 'link': '#college-anthem' },
            { 'title': 'College Houses', 'link': '#college-houses' },
            { 'title': 'Rectors', 'link': '#rectors' }
        ]
    },
    {
        'title': 'Administration',
        'link': '#administration',
        'children': [
            { 'title': 'Managing Committee', 'link': '#managing-committee' },
            { 'title': 'Rectors', 'link': '#rectors' },
            { 'title': 'Vice Rector', 'link': '#vice-rector' },
            { 'title': 'Deputy Principals', 'link': '#deputy-principals' },
            { 'title': 'Staff', 'link': '#staff' }
        ]
    },
    {
        'title': 'Academic',
        'link': '#academic',
        'children': [
            { 'title': 'Achievements', 'link': '#achievements' },
            { 'title': 'News', 'link': '#news' }
        ]
    },
    {
        'title': 'Co-Curriculum',
        'link': '#co-curriculum',
        'children': [
            { 'title': 'Clubs', 'link': '#clubs' },
            { 'title': 'Sports', 'link': '#sports' }
        ]
    },
    {
        'title': 'Students',
        'link': '#students',
        'children': [
            { 'title': 'College Sections', 'link': '#college-sections' },
            { 'title': 'Prefects', 'link': '#prefects' }
        ]
    },
]

def to_kebab_case(s):
    return re.sub(r'\s+', '-', s).lower()

def to_camel_case(s):
    parts = s.split()
    return parts[0].lower() + ''.join(word.capitalize() for word in parts[1:])

def create_file(path, content):
    with open(path, 'w') as f:
        f.write(content)

def generate_files(nav_items, base_path='src/content'):
    for item in nav_items:
        title_kebab = to_kebab_case(item['title'])

        dir_path = os.path.join(base_path, title_kebab)

        if not os.path.exists(dir_path):
            os.makedirs(dir_path)

        # Write child files
        for child in item['children']:
            child_kebab = to_kebab_case(child['title'])
            child_file_path = os.path.join(dir_path, f'{child_kebab}.mdx')
            content = f"""---
title: {child['title']}
---

import Blockquote from "../../components/patrician-publications/Blockquote.astro";
import Image from "../../components/patrician-publications/Image.astro";
export const components = {{ blockquote: Blockquote, img: Image }};

## This is a Heading 2 title

this is just a default style paragraph

you can insert images as well

![demo image](https://unsplash.com/photos/mAiFQrt9xMc/download?ixid=M3wxMjA3fDB8MXx0b3BpY3x8Q0R3dXdYSkFiRXd8fHx8fDJ8fDE3MTY2NjIzMjZ8&force=true&w=1920)

> please DO NOT use any design options. just plain image is allowed. all the
> styling will be done by SPC MEDIA UNIT.

you can *italize* or **bold-ize** the text

you can also use 
- lists
- both unordered

or 

1. ordered
2. lists are allowed

you can insert tables as well. Make sure you use proper heading rows!

# Use images provided by SPC MEDIA UNIT or CC or MIT Licensed images for copyright issues!

Have fun writing, 

Birnadin E.

Servus!
"""
            create_file(child_file_path, content)

if __name__ == '__main__':
    generate_files(NEW_NAVS)

