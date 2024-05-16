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
    link: "/",
  },{
    icon: blogIcon,
    text: "Blogs",
    link: "/patrician-publications",
  },
{
    icon: aidIcon,
    text: "Aid the SPC",
    link: "/donate",
  },{
    icon: ourSpiritIcon,
    text: "Our Spirituality",
    link: "/our-spirituality",
  },{
    icon: academiaIcon,
    text: "Academics",
    link: "/academics",
  },{
    icon: sportsIcon,
    text: "Sports",
    link: "/sports",
  },{
    icon: facilitiesIcon,
    text: "Facilities",
    link: "/facilities",
  },{
    icon: socitiesIcon,
    text: "Socities",
    link: "/socities",
  },{
    icon: alumniIcon,
    text: "Alumini",
    link: "/alumini",
  },]

export default NAVIGATION_MENU_MANIFEST;
