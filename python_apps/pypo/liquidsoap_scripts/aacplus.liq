            if bitrate == 24 then 
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 24, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 24, channels = 1), mean(!source)))
                end
            elsif bitrate == 32 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 32, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 32, channels = 1), mean(!source)))
                end
            elsif bitrate == 48 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 48, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 48, channels = 1), mean(!source)))
                end
            elsif bitrate == 64 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 64, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 64, channels = 1), mean(!source)))
                end
            elsif bitrate == 96 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 96, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 96, channels = 1), mean(!source)))
                end
            elsif bitrate == 128 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 128, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 128, channels = 1), mean(!source)))
                end
            elsif bitrate == 160 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 160, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 160, channels = 1), mean(!source)))
                end
            elsif bitrate == 192 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 192, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 192, channels = 1), mean(!source)))
                end
            elsif bitrate == 224 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 224, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 224, channels = 1), mean(!source)))
                end
            elsif bitrate == 256 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 256, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 256, channels = 1), mean(!source)))
                end
            elsif bitrate == 320 then
                if stereo then
                    ignore(output_stereo(%aacplus(bitrate = 320, channels = 2), !source))
                else
                    ignore(output_mono(%aacplus(bitrate = 320, channels = 1), mean(!source)))
                end
            end

