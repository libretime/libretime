{{ range .Versions }}<a name="{{ .Tag.Name }}"></a>

## {{ if .Tag.Previous }}[{{ .Tag.Name }}]({{ $.Info.RepositoryURL }}/compare/{{ .Tag.Previous.Name }}...{{ .Tag.Name }}){{ else }}{{ .Tag.Name }}{{ end }} ({{ datetime "2006-01-02" .Tag.Date }})

- [Release note](https://libretime.org/docs/releases/{{ .Tag.Name }}/)

{{ range .CommitGroups -}}
### {{ .Title }}

{{ range reverse .Commits -}}
- {{ if .Scope }}**{{ .Scope }}:** {{ end }}{{ .Subject }}
{{ end }}
{{ end -}}

{{- if .RevertCommits -}}
### Reverts

{{ range .RevertCommits -}}
- {{ .Revert.Header }}
{{ end }}
{{ end -}}
{{ end -}}
