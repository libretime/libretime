            if bitrate == 24 then 
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 24, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 24, channels = 1), mean(!source)))
                end
            elsif bitrate == 32 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 32, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 32, channels = 1), mean(!source)))
                end
            elsif bitrate == 48 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 48, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 48, channels = 1), mean(!source)))
                end
            elsif bitrate == 64 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 64, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 64, channels = 1), mean(!source)))
                end
            elsif bitrate == 96 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 96, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 96, channels = 1), mean(!source)))
                end
            elsif bitrate == 128 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 128, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 128, channels = 1), mean(!source)))
                end
            elsif bitrate == 160 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 160, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 160, channels = 1), mean(!source)))
                end
            elsif bitrate == 192 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 192, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 192, channels = 1), mean(!source)))
                end
            elsif bitrate == 224 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 224, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 224, channels = 1), mean(!source)))
                end
            elsif bitrate == 256 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 256, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 256, channels = 1), mean(!source)))
                end
            elsif bitrate == 320 then
                if stereo then
                    ignore(output_stereo(%aac(bitrate = 320, channels = 2), !source))
                else
                    ignore(output_mono(%aac(bitrate = 320, channels = 1), mean(!source)))
                end
            end

