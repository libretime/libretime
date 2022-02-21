import React, { FunctionComponent } from "react";
import Link from "@docusaurus/Link";

interface Props {
  version: string;
}

const TarballLink: FunctionComponent<Props> = ({ version, children }) => (
  <Link
    to={`https://github.com/libretime/libretime/releases/download/${version}/libretime-${version}.tar.gz`}
  >
    {children}
  </Link>
);

export default TarballLink;
