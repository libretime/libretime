import useBaseUrl from "@docusaurus/useBaseUrl";
import React from "react";
import clsx from "clsx";
import styles from "./HomepageFeatures.module.css";

type FeatureItem = {
  title: string;
  image: string;
  description: JSX.Element;
};

const FeatureList: FeatureItem[] = [
  {
    title: "Easy to Use",
    image: "/img/undraw_happy_news.svg",
    description: (
      <>
        Libretime was designed from the ground up to be easily installed and
        used to get your radio station on the air quickly.
      </>
    ),
  },
  {
    title: "Powerful Library Management",
    image: "/img/undraw_upload.svg",
    description: (
      <>
        Import everything from music to sweepers to full-length programs, then
        have shows and podcasts scheduled automatically.
      </>
    ),
  },
  {
    title: "AM/FM & Web",
    image: "/img/undraw_podcast.svg",
    description: (
      <>
        Audio playout to your broadcasting console or transmitter, or start an
        internet radio station directly from the cloud. Libretime can do it all.
      </>
    ),
  },
];

function Feature({ title, image, description }: FeatureItem) {
  return (
    <div className={clsx("col col--4")}>
      <div className="text--center">
        <img
          className={styles.featureSvg}
          alt={title}
          src={useBaseUrl(image)}
        />
      </div>
      <div className="text--center padding-horiz--md">
        <h3>{title}</h3>
        <p>{description}</p>
      </div>
    </div>
  );
}

export default function HomepageFeatures(): JSX.Element {
  return (
    <section className={styles.features}>
      <div className="container">
        <div className="row">
          {FeatureList.map((props, idx) => (
            <Feature key={idx} {...props} />
          ))}
        </div>
      </div>
    </section>
  );
}
