import TarballLink from '@site/src/components/TarballLink';

<p>This version was released the <strong>{props.date}</strong>.</p>

:::info

Please report new issues and/or feature requests in [the issue tracker](https://github.com/libretime/libretime/issues).

For general discussion or if you need help, you can join the [discourse](https://discourse.libretime.org/) forum or chat on [#libretime:matrix.org](https://matrix.to/#/#libretime:matrix.org).

:::

<p>
The full tarball for the <code>{props.version}</code> release of
LibreTime is available <TarballLink version={props.version}>here</TarballLink>.
</p>
