import homeIcon from "~/assets/nav-menu-home.svg?raw";
import ourSpiritIcon from "~/assets/nav-menu-our-spirit.svg?raw";
import blogIcon from "~/assets/nav-menu-blog.svg?raw";
import aidIcon from "~/assets/nav-menu-aid.svg?raw";
import academiaIcon from "~/assets/nav-menu-academia.svg?raw";
import sportsIcon from "~/assets/nav-menu-sports.svg?raw";
import facilitiesIcon from "~/assets/nav-menu-facilities.svg?raw";
import socitiesIcon from "~/assets/nav-menu-socities.svg?raw";
import alumniIcon from "~/assets/nav-menu-alumni.svg?raw";

const NAVIGATION_MENU_MANIFEST = [
    {
        icon: homeIcon,
        text: "Home",
        link: "/"
    }, {
        icon: blogIcon,
        text: "Blogs",
        link: "/blogs"
    },
    {
        icon: aidIcon,
        text: "Aid the SPC",
        link: "/donate"
    }, {
        icon: ourSpiritIcon,
        text: "Our Spirituality",
        link: "/our-spirituality"
    }, {
        icon: academiaIcon,
        text: "Academics",
        link: "/academics"
    }, {
        icon: sportsIcon,
        text: "Sports",
        link: "/sports"
    }, {
        icon: facilitiesIcon,
        text: "Facilities",
        link: "/facilities"
    }, {
        icon: socitiesIcon,
        text: "Socities",
        link: "/socities"
    }, {
        icon: alumniIcon,
        text: "Alumini",
        link: "/alumini"
    }];

export default NAVIGATION_MENU_MANIFEST;
export const NEW_NAVS = [
    { title: "Home", link: "", children: [] },
    {
        title: "Alma Mater",
        link: "alma-mater",
        children: [
            { title: "History of the College", link: "history-of-the-college" },
            { title: "Our Spirituality", link: "our-spirituality" },
            { title: "Founders", link: "founders" },
            { title: "Motto, Vision & Mission", link: "motto-vision-mission" },
            { title: "Coat of Arms", link: "coat-of-arms" },
            { title: "College Anthem", link: "college-anthem" },
            { title: "College Houses", link: "college-houses" },
            { title: "Rectors", link: "rectors" }
        ]
    },
    {
        title: "Administration",
        link: "administration",
        children: [
            { title: "Managing Committee", link: "managing-committee" },
            { title: "Rectors", link: "rectors" },
            { title: "Vice Rector", link: "vice-rector" },
            { title: "Deputy Principals", link: "deputy-principals" },
            { title: "Staff", link: "staff" }
        ]
    },
    {
        title: "Academics",
        link: "academics",
        children: [
            { title: "Overview", link: "overview" },
            { title: "Achievements", link: "achievements" },
            { title: "News", link: "news" }
        ]
    },
    {
        title: "Co-Curriculum",
        link: "co-curriculum",
        children: [
            { title: "Clubs", link: "clubs" },
            { title: "Sports", link: "sports" }
        ]
    },
    {
        title: "Students",
        link: "students",
        children: [
            { title: "College Sections", link: "college-sections" },
            { title: "Prefects", link: "prefects" }
        ]
    },
    { title: "Blogs", link: "blogs", children: [] },
    { title: "Projects", link: "projects", children: [] },
    { title: "Facilities", link: "facilities", children: [] },
    { title: "Gallery", link: "gallery", children: [] }
];
