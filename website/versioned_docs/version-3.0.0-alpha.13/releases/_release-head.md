import TarballLink from '@site/src/components/TarballLink';

<p>This version was released the <strong>{props.date}</strong>.</p>

:::info

Please report new issues and/or feature requests in [the issue tracker](https://github.com/libretime/libretime/issues).

Join our [discourse](https://discourse.libretime.org/) or chat with us on our [mattermost](https://chat.libretime.org/) if you need help and for general discussion.

:::

<p>
The full tarball for the <code>{props.version}</code> release of
LibreTime is available <TarballLink version={props.version}>here</TarballLink>.
</p>
